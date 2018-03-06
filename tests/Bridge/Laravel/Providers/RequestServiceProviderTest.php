<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Interfaces\RequestInterface;
use EoneoPay\External\Bridge\Laravel\Providers\RequestServiceProvider;
use Tests\EoneoPay\External\BridgeProvidersTestCase;

class RequestServiceProviderTest extends BridgeProvidersTestCase
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
