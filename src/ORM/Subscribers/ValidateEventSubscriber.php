<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Column;
use EoneoPay\External\Logger\Interfaces\LoggerInterface;
use EoneoPay\External\Logger\Logger;
use EoneoPay\External\ORM\Exceptions\DefaultEntityValidationFailedException;
use EoneoPay\External\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\AnnotationReader;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

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
     * @param null|\EoneoPay\External\Logger\Interfaces\LoggerInterface $logger
     */
    public function __construct(ValidationFactory $validationFactory, ?LoggerInterface $logger = null)
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
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
            $exceptionClass = \method_exists($entity, 'getValidationFailedException')
                ? $entity->getValidationFailedException()
                : DefaultEntityValidationFailedException::class;

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
        try {
            $columns = (new AnnotationReader())->getClassPropertyAnnotation(\get_class($entity), Column::class);
        } catch (\Exception $exception) {
            $this->logger->exception($exception);

            return [];
        }

        $contents = [];
        foreach ($columns as $property => $column) {
            $getter = \sprintf('get%s', \ucfirst($property));

            /** @var Column $column */
            $contents[$column->name ?? $property] = $entity->$getter();
        }

        return $contents;
    }
}
