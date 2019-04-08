<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Subscribers;

use EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber;
use Tests\EoneoPay\Externals\TestCase;

class SoftDeleteEventSubscriberTest extends TestCase
{
    /**
     * EventSubscriber should return expected list of events.
     *
     * @return void
     */
    public function testGetSubscribedEventsReturnsExpectedListOfEvents(): void
    {
        $expected = ['loadClassMetadata', 'onFlush', 'postFlush'];

        self::assertEquals($expected, (new SoftDeleteEventSubscriber())->getSubscribedEvents());
    }
}
