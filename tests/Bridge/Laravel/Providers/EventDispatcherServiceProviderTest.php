<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use EoneoPay\Externals\Bridge\Laravel\Providers\EventDispatcherServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\PsrEventDispatcher;
use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface;
use Eonx\TestUtils\Stubs\Vendor\Illuminate\Container\ContainerStub;
use Eonx\TestUtils\TestCases\Unit\LaravelServiceProviderTestCase;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcherContract;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\EventDispatcherServiceProvider
 */
class EventDispatcherServiceProviderTest extends LaravelServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            EventDispatcherInterface::class => EventDispatcher::class,
            PsrEventDispatcherInterface::class => PsrEventDispatcher::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainer(): ContainerStub
    {
        $container = parent::getContainer();
        $container->bind(
            IlluminateDispatcherContract::class,
            static function () use ($container): IlluminateDispatcher {
                return new IlluminateDispatcher($container);
            }
        );

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return EventDispatcherServiceProvider::class;
    }
}
