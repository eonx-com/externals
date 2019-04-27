<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider;
use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
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
        $application = new ApplicationStub();
        $application->instance(LoggerInterface::class, new Logger());

        // Run registration
        (new HttpClientServiceProvider($application))->register();

        // Ensure we get back a LoggingClient
        $client = $application->get(ClientInterface::class);
        self::assertInstanceOf(LoggingClient::class, $client);

        // Ensure we get back a LoggingClient
        self::assertSame($client, $application->get(PsrClientInterface::class));

        // Ensure we configure the inner client
        self::assertInstanceOf(
            Client::class,
            $application->get('eoneopay_externals.client')
        );

        // Ensure we configure the guzzle client
        self::assertInstanceOf(
            GuzzleClientInterface::class,
            $application->get('eoneopay_externals.http_client')
        );
    }
}
