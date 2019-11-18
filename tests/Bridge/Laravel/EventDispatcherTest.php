<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Tests\EoneoPay\Externals\Stubs\Events\EventStub;
use Tests\EoneoPay\Externals\Stubs\Events\StoppableEventStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Events\DispatcherStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\EventDispatcher
 */
class EventDispatcherTest extends TestCase
{
    /**
     * Dispatcher returns the expected event after dispatch is called.
     *
     * @return void
     */
    public function testDispatch(): void
    {
        $dispatcher = new DispatcherStub();
        $event = new EventStub();
        $eventDispatcher = new EventDispatcher($dispatcher);
        $expectedCall = [[
            'event' => $event,
            'payload' => [],
            'halt' => false
        ]];

        $response = $eventDispatcher->dispatch($event);

        self::assertSame($event, $response);
        self::assertSame($expectedCall, $dispatcher->getCall('dispatch'));
    }

    /**
     * Dispatcher returns the expected (stoppable) event after dispatch is called.
     *
     * @return void
     */
    public function testDispatchStoppable(): void
    {
        $dispatcher = new DispatcherStub();
        $event = new StoppableEventStub();
        $eventDispatcher = new EventDispatcher($dispatcher);
        $expectedCall = [[
            'event' => $event,
            'payload' => [],
            'halt' => true
        ]];

        $response = $eventDispatcher->dispatch($event);

        self::assertSame($event, $response);
        self::assertSame($expectedCall, $dispatcher->getCall('dispatch'));
    }
}
