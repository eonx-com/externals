<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\RequestServiceProvider;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class RequestServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider bind our request interface into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        (new RequestServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(RequestInterface::class, $this->getApplication()->get(RequestInterface::class));
    }
}
