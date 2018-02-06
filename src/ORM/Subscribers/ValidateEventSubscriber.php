<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use EoneoPay\External\Logger\Interfaces\LoggerInterface;
use EoneoPay\External\Logger\Logger;
use EoneoPay\External\ORM\Exceptions\DefaultEntityValidationException;
use EoneoPay\External\ORM\Interfaces\EntityInterface;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use ReflectionClass;
use ReflectionException;

class ValidateEventSubscriber implements EventSubscriber
{
    /**
     * @var \EoneoPay\External\Logger\Interfaces\LoggerInterface
     */
    private $logger;

    /**
     * @var ValidationFactory
     */
    private $validationFactory;

    /**
     * ValidateEventSubscriber constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validationFactory
     * @param \EoneoPay\External\Logger\Interfaces\LoggerInterface $logger
     */
    public function __construct(ValidationFactory $validationFactory, LoggerInterface $logger)
    {
        $this->validationFactory = $validationFactory;
        $this->logger = $logger ?? new Logger();
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
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
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException Inherited, if validation fails
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Validate entity against rule set on update
     *
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException Inherited, if validation fails
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Call validator on an object
     *
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If validation fails
     */
    private function callValidator(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof EntityInterface
            || !\method_exists($entity, 'getRules')
            || !\is_array($entity->getRules())) {
            return;
        }

        try {
            /** @var Validator $validator */
            $validator = $this->validationFactory->make($this->getEntityContents($entity), $entity->getRules());
            $validator->validate();
        } catch (ValidationException $exception) {
            $exceptionClass = \method_exists($entity, 'getValidationException')
                ? $entity->getValidationException()
                : DefaultEntityValidationException::class;

            throw new $exceptionClass(
                $exception->getMessage(),
                $exception->getCode(),
                $exception,
                $exception->errors()
            );
        }
    }

    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray().
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity
     *
     * @return array
     */
    private function getEntityContents(EntityInterface $entity): array
    {
        // Get properties available for this model
        try {
            $reflection = new ReflectionClass(\get_class($entity));
        } catch (ReflectionException $exception) {
            $this->logger->exception($exception);

            // If an exception occurred, return no contents
            return [];
        }

        $properties = $reflection->getProperties();

        // Get property values
        $contents = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $contents[$property->name] = $property->getValue($entity);
        }

        return $contents;
    }
}
