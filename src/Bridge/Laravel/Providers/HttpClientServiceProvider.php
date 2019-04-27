<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\HttpClient\StreamParser;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Utils\XmlConverter;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface as PsrClientInterface;

class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Defined to allow an application to override the Guzzle client used for External's HTTP
        // requests.
        $this->app->singleton('eoneopay_externals.http_client', GuzzleClient::class);

        // Define the inner client that is used as part of the LoggingClient
        $this->app->singleton('eoneopay_externals.client', static function (Container $app): Client {
            return new Client(
                $app->make('eoneopay_externals.http_client'),
                new ExceptionHandler(),
                new StreamParser(new XmlConverter())
            );
        });

        // Wrap a Client in a LoggingClient to get logging functionality.
        $this->app->singleton(ClientInterface::class, static function (Container $app): LoggingClient {
            return new LoggingClient(
                $app->make('eoneopay_externals.client'),
                $app->make(LoggerInterface::class)
            );
        });

        // If someone is asking for a PsrClientInterface, lets give them our implementation.
        $this->app->alias(PsrClientInterface::class, ClientInterface::class);
    }
}
