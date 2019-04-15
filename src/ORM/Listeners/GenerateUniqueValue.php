<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Exceptions\UniqueValueNotGeneratedException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueInterface;
use EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueWithCallbackInterface;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use EoneoPay\Utils\CheckDigit;
use EoneoPay\Utils\Interfaces\GeneratorInterface;

/**
 * Doctrine listener that will be applied to entities that have the following interface implemented
 *
 * @see \EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueInterface
 * @see \EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueWithCallbackInterface
 *
 * Callback is optional, but interface requires entity to declare it
 */
final class GenerateUniqueValue
{
    /**
     * The generator.
     *
     * @var \EoneoPay\Utils\Interfaces\GeneratorInterface
     */
    private $generator;

    /**
     * Translator instance
     *
     * @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface
     */
    private $translator;

    /**
     * Initialise the attribute.
     *
     * @param \EoneoPay\Utils\Interfaces\GeneratorInterface $generator Generator to generate a random string
     * @param \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator Translator instance
     */
    public function __construct(GeneratorInterface $generator, TranslatorInterface $translator)
    {
        $this->generator = $generator;
        $this->translator = $translator;
    }

    /**
     * Generate unique value for property if required
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Event arguments
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \EoneoPay\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If value can't be generated
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $this->checkEntity($eventArgs->getEntity());

        if (($entity instanceof GenerateUniqueValueInterface) === false) {
            return;
        }

        // Generate value, will throw exception if not possible
        $randomValue = $this->generateValue($entity, $eventArgs);

        $setter = [$entity, \sprintf('set%s', \ucfirst($entity->getGeneratedProperty()))];

        // If setter isn't callable, abort - this is only here for safety since base entity provides __call
        if (\is_callable($setter) === false) {
            return; // @codeCoverageIgnore
        }

        $setter($randomValue);

        if (($entity instanceof GenerateUniqueValueWithCallbackInterface) === true) {
            /**
             * @var \EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueWithCallbackInterface $entity
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            $entity->getGeneratedPropertyCallback($randomValue);
        }
    }

    /**
     * Determine if this entity requires generation
     *
     * @param mixed $entity The entity to check
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueInterface|null
     */
    private function checkEntity($entity): ?GenerateUniqueValueInterface
    {
        // Entity must be an application entity, implement the correct interface and have the genrator enabled
        return (($entity instanceof EntityInterface) === true &&
            ($entity instanceof GenerateUniqueValueInterface) === true &&
            $entity->areGeneratorsEnabled() === true) ? $entity : null;
    }

    /**
     * Generate a random value for this entity
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueInterface $entity Entity to generate for
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Life cycle call back arguments
     *
     * @return string
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \EoneoPay\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If value can't be generated
     */
    private function generateValue(GenerateUniqueValueInterface $entity, LifecycleEventArgs $eventArgs): string
    {
        // Get entity manager instance and repository
        $entityManager = new EntityManager($eventArgs->getEntityManager());
        $repository = $entityManager->getRepository(\get_class($entity));

        // Configure static settings
        $hasCheckDigit = $entity->hasGeneratedPropertyCheckDigit();
        $length = $hasCheckDigit === true ?
            $entity->getGeneratedPropertyLength() - 1 :
            $entity->getGeneratedPropertyLength();
        $property = $entity->getGeneratedProperty();


        // Try 100 times to obtain a unique value
        for ($count = 0; $count < 100; $count++) {
            $randomValue = $this->generator->randomString(
                $length,
                GeneratorInterface::RANDOM_INCLUDE_ALPHA_UPPERCASE |
                GeneratorInterface::RANDOM_INCLUDE_INTEGERS |
                GeneratorInterface::RANDOM_EXCLUDE_SIMILAR
            );

            // If entity requires a check-digit, calculate it
            if ($hasCheckDigit === true) {
                $randomValue = \sprintf('%s%s', $randomValue, (new CheckDigit())->calculate($randomValue));
            }

            if ($repository->count([$property => $randomValue]) !== 0) {
                continue;
            }

            return $randomValue;
        }

        // If no return was completed, throw exception
        throw new UniqueValueNotGeneratedException(
            $this->translator->trans('exceptions.external.doctrine.unique_generation_failed')
        );
    }
}
