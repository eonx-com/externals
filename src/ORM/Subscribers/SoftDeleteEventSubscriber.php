<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

final class SoftDeleteEventSubscriber extends SoftDeleteableListener
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
     * @inheritdoc
     *
     * @throws \Exception If there is a database error
     */
    public function onFlush(EventArgs $args): void
    {
        $eventAdapter = $this->getEventAdapter($args);
        $objectManager = $eventAdapter->getObjectManager();

        // Ensure object manager implements entity manager interface
        if (($objectManager instanceof EntityManagerInterface) === false) {
            return;
        }

        /**
         * @var \Doctrine\ORM\EntityManagerInterface $objectManager
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
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
     * Detach soft deleted items from entity manager
     *
     * @param \Doctrine\Common\EventArgs $args Lifecycle event args used by flush
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
