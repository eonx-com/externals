<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ClientOptions;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Interfaces\ClientOptionsInterface;
use GuzzleHttp\Client as Guzzle;
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

        $client = $this->createInstance(new MockHandler([
            new TransferException('An error occurred')
        ]));

        $client->request('get', 'test');
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

        $client = $this->createInstance(new MockHandler([
            new RequestException(
                'An error occurred',
                new Request('GET', 'test'),
                new Response(500, [], 'error')
            )
        ]));

        $client->request('get', 'test');
    }

    /**
     * Test handling of request exception without response body
     *
     * @return void
     */
    public function testHandlingGuzzleRequestExceptionWithoutBody(): void
    {
        $client = $this->createInstance(new MockHandler([
            new RequestException('An error occurred', new Request('GET', 'test'))
        ]));

        try {
            $client->request('get', 'test');
        } catch (InvalidApiResponseException $exception) {
            self::assertSame('{"exception":"An error occurred"}', $exception->getResponse()->getContent());

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
        $client = $this->createInstance(new MockHandler([
            new Response(200, [], 'ok')
        ]));

        $result = $client->request('get', 'test');

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
        $client = $this->createInstance(new MockHandler([
            new Response(200, [], 'ok')
        ]));

        $result = $client->sendRequest(new Request('post', '/'));

        self::assertSame(200, $result->getStatusCode());
        self::assertSame('ok', (string)$result->getBody());
    }

    /**
     * Test processing a standard request
     *
     * @return void
     */
    public function testRequestSendRequestingException(): void
    {
        $client = $this->createInstance(new MockHandler([
            new RequestException('An error occurred', new Request('GET', 'test'))
        ]));

        try {
            $client->sendRequest(new Request('post', '/'));
        } catch (InvalidApiResponseException $exception) {
            self::assertSame('{"exception":"An error occurred"}', $exception->getResponse()->getContent());

            return;
        }

        self::fail('An exception was not thrown');
    }

    /**
     * Create client instance
     *
     * @param \GuzzleHttp\Handler\MockHandler $handler Guzzle mock handler
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ClientOptionsInterface|null $clientOptions
     *
     * @return \EoneoPay\Externals\HttpClient\Client
     */
    private function createInstance(
        MockHandler $handler,
        ?ClientOptionsInterface $clientOptions = null
    ): Client {
        // Create guzzle with mock response
        return new Client(
            new Guzzle(['handler' => $handler]),
            new ExceptionHandler(),
            $clientOptions ?? new ClientOptions()
        );
    }
}
