<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class ContainerServiceProviderTest extends LaravelBridgeProvidersTestCase
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
        (new ContainerServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(ContainerInterface::class, $this->getApplication()->get(ContainerInterface::class));
    }
}
