<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Mockery\MockInterface;
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
        /** @var \Illuminate\Events\Dispatcher $illuminateDispatcher */
        $illuminateDispatcher = $this->mockIlluminateDispatcherForListen();
        (new EventDispatcher($illuminateDispatcher))->listen([], 'my-listener');

        // Assertions done within Mockery
        self::assertTrue(true);
    }

    /**
     * Mock IlluminateDispatcher to test listen.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    private function mockIlluminateDispatcherForListen(): MockInterface
    {
        $dispatcher = \Mockery::mock(IlluminateDispatcher::class);

        $dispatcher
            ->shouldReceive('listen')
            ->once()
            ->withArgs(function ($events, $listener): bool {
                return \is_array($events) && $listener === 'my-listener';
            })
            ->andReturnNull();

        return $dispatcher;
    }
}
