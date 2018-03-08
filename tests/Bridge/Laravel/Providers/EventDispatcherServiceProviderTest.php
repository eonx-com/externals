<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Providers\EventDispatcherServiceProvider;
use EoneoPay\External\EventDispatcher\Interfaces\EventDispatcherInterface;
use Tests\EoneoPay\External\LaravelBridgeProvidersTestCase;

class EventDispatcherServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider register container.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        (new EventDispatcherServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(
            EventDispatcherInterface::class,
            $this->getApplication()->get(EventDispatcherInterface::class)
        );
    }
}
