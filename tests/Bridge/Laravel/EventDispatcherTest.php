<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Tests\EoneoPay\Externals\TestCase;

class EventDispatcherTest extends TestCase
{
    /**
     * Dispatcher should return array or null based on $halt parameter.
     *
     * @return void
     */
    public function testDispatch(): void
    {
        $illuminateDispatcher = new IlluminateDispatcher();
        $eventDispatcher = new EventDispatcher($illuminateDispatcher);

        self::assertInternalType('array', $eventDispatcher->dispatch('my-event'));
        self::assertNull($eventDispatcher->dispatch('my-event', null, true));
    }
}
