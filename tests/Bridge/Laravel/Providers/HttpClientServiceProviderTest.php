<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HttpClientServiceProvider;
use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface;
use EoneoPay\Externals\HttpClient\StreamParser;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
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

        self::assertInstanceOf(Client::class, $app->get(ClientInterface::class));
        self::assertInstanceOf(ExceptionHandler::class, $app->get(ExceptionHandlerInterface::class));
        self::assertInstanceOf(StreamParser::class, $app->get(StreamParserInterface::class));
    }
}
