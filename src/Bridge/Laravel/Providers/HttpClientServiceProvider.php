<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface;
use EoneoPay\Externals\HttpClient\StreamParser;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
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
        // Define a Guzzle binding so Client can be created
        $this->app->bind(GuzzleClientInterface::class, GuzzleClient::class);

        // Alias Client to PsrClientInterface
        $this->app->alias(ClientInterface::class, PsrClientInterface::class);
        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(ExceptionHandlerInterface::class, ExceptionHandler::class);
        $this->app->bind(StreamParserInterface::class, StreamParser::class);
    }
}
