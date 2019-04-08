<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\RequestServiceProvider;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\RequestServiceProvider
 */
class RequestServiceProviderTest extends TestCase
{
    /**
     * Test provider bind our request interface into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Register services
        (new RequestServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(RequestInterface::class, $application->get(RequestInterface::class));
    }
}
