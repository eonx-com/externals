<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ClientFactory;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
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
        // Register the Http ClientFactory
        $this->app->singleton(ClientFactory::class);

        // Register a default http service that comes with a minimally configured
        // Guzzle instance inside.
        $this->app->singleton('http', static function (Container $app): LoggingClient {
            $client = new Client(
                new GuzzleClient(),
                new ExceptionHandler(),
                new StreamParser(new XmlConverter())
            );

            return new LoggingClient($client, $app->make(LoggerInterface::class));
        });

        // If someone is asking for a PsrClientInterface, lets give them our implementation.
        $this->app->alias('http', PsrClientInterface::class);
    }
}
