<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Exceptions\NetworkException;
use EoneoPay\Externals\HttpClient\StreamParser;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\Client
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test entity manager
 */
class ClientTest extends TestCase
{
    /**
     * Test handling of a guzzle generic exception
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testHandlingGuzzleException(): void
    {
        $this->expectException(InvalidApiResponseException::class);

        $instance = $this->createInstance(new MockHandler([
            new TransferException('An error occured')
        ]));

        $instance->request('get', 'test');
    }

    /**
     * Test handling of a guzzle request exception
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testHandlingGuzzleRequestException(): void
    {
        $this->expectException(InvalidApiResponseException::class);

        $instance = $this->createInstance(new MockHandler([
            new RequestException(
                'An error occured',
                new Request('GET', 'test'),
                new Response(500, [], 'error')
            )
        ]));

        $instance->request('get', 'test');
    }

    /**
     * Test handling of request exception without response body
     *
     * @return void
     */
    public function testHandlingGuzzleRequestExceptionWithoutBody(): void
    {
        $instance = $this->createInstance(new MockHandler([
            new RequestException('An error occured', new Request('GET', 'test'))
        ]));

        try {
            $instance->request('get', 'test');
        } catch (InvalidApiResponseException $exception) {
            self::assertSame('{"exception":"An error occured"}', $exception->getResponse()->getContent());

            return;
        }

        self::fail('An exception was not thrown');
    }

    /**
     * Test processing a standard request
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function testRequestProcessing(): void
    {
        $instance = $this->createInstance(new MockHandler([
            new Response(200, [], 'ok')
        ]));

        $result = $instance->request('get', 'test');

        self::assertSame(200, $result->getStatusCode());
        self::assertSame('ok', $result->getContent());
    }

    /**
     * Test processing a standard request
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function testRequestSendRequesting(): void
    {
        $instance = $this->createInstance(new MockHandler([
            new Response(200, [], 'ok')
        ]));

        $result = $instance->sendRequest(new Request('post', '/'));

        self::assertSame(200, $result->getStatusCode());
        self::assertSame('ok', $result->getBody()->__toString());
    }

    /**
     * Test processing a standard request
     *
     * @return void
     */
    public function testRequestSendRequestingException(): void
    {
        $instance = $this->createInstance(new MockHandler([
            new RequestException('An error occured', new Request('GET', 'test'))
        ]));

        try {
            $instance->sendRequest(new Request('post', '/'));
        } catch (InvalidApiResponseException $exception) {
            self::assertSame('{"exception":"An error occured"}', $exception->getResponse()->getContent());

            return;
        }

        self::fail('An exception was not thrown');
    }

    /**
     * Test network failure
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function testSendRequestNetworkException(): void
    {
        $expected = new ConnectException('An error occured', new Request('GET', 'test'));

        $instance = $this->createInstance(new MockHandler([
            $expected
        ]));

        try {
            $instance->sendRequest(new Request('post', '/'));
        } catch (NetworkException $exception) {
            self::assertSame($expected, $exception->getPrevious());

            return;
        }

        self::fail('An exception was not thrown');
    }

    /**
     * Create client instance
     *
     * @param \GuzzleHttp\Handler\MockHandler $handler Guzzle mock handler
     *
     * @return \EoneoPay\Externals\HttpClient\Client
     */
    private function createInstance(MockHandler $handler): Client
    {
        // Create guzzle with mock response
        return new Client(
            new Guzzle(['handler' => $handler]),
            new ExceptionHandler(),
            new StreamParser()
        );
    }
}
