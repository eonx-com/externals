<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;

class EventManagerStub extends EventManager
{
    /**
     * Loaded event subscribers
     *
     * @var \Doctrine\Common\EventSubscriber[]
     */
    private $subscribers = [];

    /**
     * Adds an EventSubscriber. The subscriber is asked for all the events it is
     * interested in and added as a listener for these events.
     *
     * @param \Doctrine\Common\EventSubscriber $subscriber The subscriber
     *
     * @return void
     */
    public function addEventSubscriber(EventSubscriber $subscriber): void
    {
        $this->subscribers[] = $subscriber;

        parent::addEventSubscriber($subscriber);
    }

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\EventSubscriber[]
     */
    public function getSubscribers(): array
    {
        return $this->subscribers;
    }
}
