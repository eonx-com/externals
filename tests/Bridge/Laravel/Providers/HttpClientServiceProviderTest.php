<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider;
use EoneoPay\Externals\HttpClient\ClientFactory;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider
 */
class HttpClientServiceProviderTest extends TestCase
{
    /**
     * Test provider register container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $app = new ApplicationStub();
        $app->instance(LoggerInterface::class, new Logger());

        // Run registration
        (new HttpClientServiceProvider($app))->register();

        // Ensure we get back a LoggingClient
        $client = $app->get('http');
        self::assertInstanceOf(LoggingClient::class, $client);

        // Ensure we get back a LoggingClient
        self::assertSame($client, $app->get(PsrClientInterface::class));

        self::assertInstanceOf(ClientFactory::class, $app->get(ClientFactory::class));
    }
}
