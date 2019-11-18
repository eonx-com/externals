<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Tests\EoneoPay\Externals\Stubs\Events\EventStub;
use Tests\EoneoPay\Externals\Stubs\Events\StoppableEventStub;
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
        $event = new EventStub();
        $eventDispatcher = new EventDispatcher(new IlluminateDispatcher());

        $response = $eventDispatcher->dispatch($event);

        self::assertSame($event, $response);
    }

    /**
     * Dispatcher returns the expected (stoppable) event after dispatch is called.
     *
     * @return void
     */
    public function testDispatchStoppable(): void
    {
        $event = new StoppableEventStub();
        $eventDispatcher = new EventDispatcher(new IlluminateDispatcher());

        $response = $eventDispatcher->dispatch($event);

        self::assertSame($event, $response);
    }
}
