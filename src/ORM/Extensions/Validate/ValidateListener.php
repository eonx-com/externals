<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions\Validate;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use EoneoPay\External\ORM\Extensions\Listener;

class ValidateListener extends Listener
{
    /**
     * Validate entity against rule set on insert
     *
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return void
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Validate entity against rule set on update
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs
     *
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Get the trait associated with the listener
     *
     * @return string
     */
    protected function getTrait(): string
    {
        return Validates::class;
    }

    /**
     * Call validator on an object
     *
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return void
     */
    private function callValidator(LifecycleEventArgs $eventArgs): void
    {
        // Only action on entities which are validatable
        if ($this->canRun($eventArgs)) {
            $eventArgs->getEntity()->validate();
        }
    }
}
