<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\EventDispatcher
 */
class EventDispatcherTest extends TestCase
{
    /**
     * Dispatcher should return array or null based on $halt parameter.
     *
     * @return void
     */
    public function testDispatch(): void
    {
        $eventDispatcher = new EventDispatcher(new IlluminateDispatcher());

        self::assertIsArray($eventDispatcher->dispatch('my-event'));
        self::assertNull($eventDispatcher->dispatch('my-event', null, true));
    }

    /**
     * Dispatcher should call illuminate dispatcher to configure listener for given events.
     *
     * @return void
     */
    public function testListen(): void
    {
        $dispatcher = new IlluminateDispatcher();
        $eventDispatcher = new EventDispatcher($dispatcher);

        // Ensure listener doesn't exist
        self::assertFalse($dispatcher->hasListeners('test'));

        $eventDispatcher->listen(['test'], 'my-listener');

        // Ensure listener was added
        self::assertTrue($dispatcher->hasListeners('test'));
    }
}
