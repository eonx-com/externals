<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\RequestServiceProvider;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Illuminate\Http\Request as HttpRequest;
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
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Static access to HttpRequest required to get proxies.
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Register services
        (new RequestServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(RequestInterface::class, $application->get(RequestInterface::class));

        // Ensure the trusted proxy headers are set
        self::assertSame(['127.0.0.1', '10.0.0.0/8'], HttpRequest::getTrustedProxies());
        self::assertSame(HttpRequest::HEADER_X_FORWARDED_AWS_ELB, HttpRequest::getTrustedHeaderSet());
    }
}
