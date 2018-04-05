<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Providers\RequestServiceProvider;
use EoneoPay\External\Request\Interfaces\RequestInterface;
use Tests\EoneoPay\External\LaravelBridgeProvidersTestCase;

class RequestServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider bind our request interface into container.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        (new RequestServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(RequestInterface::class, $this->getApplication()->get(RequestInterface::class));
    }
}