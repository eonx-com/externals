<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\PsrEventDispatcher;
use stdClass;
use Tests\EoneoPay\Externals\Stubs\Bridge\Laravel\EventDispatcherStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\PsrEventDispatcher
 */
class PsrEventDispatcherTest extends TestCase
{
    /**
     * Test that the PsrEventDispatcher dispatches to the laravel dispatcher.
     *
     * @return void
     */
    public function testDispatch(): void
    {
        $innerDispatcher = new EventDispatcherStub();
        $dispatcher = new PsrEventDispatcher($innerDispatcher);

        $event = new stdClass();

        $expected = [
            'event' => $event,
            'payload' => null,
            'halt' => null
        ];

        $dispatcher->dispatch($event);

        self::assertSame([$expected], $innerDispatcher->getCalls('dispatch'));
    }
}
