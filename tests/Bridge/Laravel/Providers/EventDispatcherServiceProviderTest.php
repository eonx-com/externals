<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use EoneoPay\Externals\Bridge\Laravel\Providers\EventDispatcherServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcherContract;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\EventDispatcherServiceProvider
 */
class EventDispatcherServiceProviderTest extends TestCase
{
    /**
     * Test provider register container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Bind illuminate dispatcher
        $application->bind(
            IlluminateDispatcherContract::class,
            static function () use ($application): IlluminateDispatcher {
                return new IlluminateDispatcher($application);
            }
        );

        // Run registration
        (new EventDispatcherServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(
            EventDispatcher::class,
            $application->get(PsrEventDispatcherInterface::class)
        );
    }
}
