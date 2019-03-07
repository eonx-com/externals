<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Common\EventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

class SoftDeleteEventSubscriber extends SoftDeleteableListener
{
    /**
     * Objects soft-deleted on flush.
     *
     * @var mixed[]
     */
    private $softDeletedObjects = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return \array_merge(parent::getSubscribedEvents(), ['postFlush']);
    }

    /**
     * Cache soft deleted objects to detach them from the entity manager on postFlush.
     *
     * @param \Doctrine\Common\EventArgs $args
     *
     * @return void
     *
     * @throws \Exception If there is a database error
     */
    public function onFlush(EventArgs $args): void
    {
        $eventAdapter = $this->getEventAdapter($args);
        /** @var \Doctrine\ORM\EntityManagerInterface $objectManager */
        $objectManager = $eventAdapter->getObjectManager();
        $unitOfWork = $objectManager->getUnitOfWork();

        foreach ($eventAdapter->getScheduledObjectDeletions($unitOfWork) as $object) {
            $meta = $objectManager->getClassMetadata(\get_class($object));
            $config = $this->getConfiguration($objectManager, $meta->name);

            if ($config['softDeleteable'] ?? false) {
                $this->softDeletedObjects[] = $object;
            }
        }

        // Parent must be called at the end because it removes objects from unitOfWork
        parent::onFlush($args);
    }

    /**
     * Detach soft-deleted objects from object manager.
     *
     * @param \Doctrine\Common\EventArgs $args
     *
     * @return void
     */
    public function postFlush(EventArgs $args): void
    {
        $eventAdapter = $this->getEventAdapter($args);
        $objectManager = $eventAdapter->getObjectManager();

        foreach ($this->softDeletedObjects as $index => $object) {
            $objectManager->detach($object);

            unset($this->softDeletedObjects[$index]);
        }
    }
}
