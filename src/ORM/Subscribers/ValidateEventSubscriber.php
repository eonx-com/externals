<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\ValidatableInterface;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;

class ValidateEventSubscriber implements EventSubscriber
{
    /**
     * Translator instance
     *
     * @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface
     */
    private $translator;

    /**
     * Validator instance
     *
     * @var \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface
     */
    private $validator;

    /**
     * ValidateEventSubscriber constructor.
     *
     * @param \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator
     * @param \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface $validator
     */
    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator)
    {
        $this->translator = $translator;
        $this->validator = $validator;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    /**
     * Validate entity against rule set on insert
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Validate entity against rule set on update
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /** @noinspection PhpDocRedundantThrowsInspection Exception thrown dynamically */
    /**
     * Call validator on an object
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    private function callValidator(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        // If the entity isn't validatable, return
        if (($entity instanceof ValidatableInterface) === false) {
            return;
        }

        // If validation passes, return
        /**
         * @var \EoneoPay\Externals\ORM\Interfaces\ValidatableInterface $entity
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
        if ($this->validator->validate($this->getEntityContents($entity), $entity->getRules()) === true) {
            return;
        }

        // Get exception class from entity and throw it
        $exceptionClass = $entity->getValidationFailedException();

        throw new $exceptionClass(
            $this->translator->trans('exceptions.validation.failed'),
            null,
            null,
            $this->validator->getFailures()
        );
    }

    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray().
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return mixed[]
     */
    private function getEntityContents(EntityInterface $entity): array
    {
        $contents = [];
        foreach ($entity->getProperties() as $property) {
            $getter = \sprintf('get%s', \ucfirst($property));


            // Remove backticks from column name
            $contents[\trim($property, '`')] = $entity->$getter();
        }

        return $contents;
    }
}
