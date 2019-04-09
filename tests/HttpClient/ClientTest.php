<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler\LogHandlerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\Client
 */
class ClientTest extends TestCase
{
    /**
     * Test exceptions are logged correctly
     *
     * @return void
     */
    public function testExceptionLogging(): void
    {
        $handler = new LogHandlerStub();

        try {
            $this->createInstance(
                new MockHandler([
                    new RequestException('An error occured', new Request('GET', 'test'), new Response(500, [], 'error'))
                ]),
                new Logger(null, $handler)
            )->request('get', 'test');
        } catch (InvalidApiResponseException $exception) {
            // Capture previous exception
            $previous = $exception->getPrevious();

            self::assertCount(3, $handler->getLogs());
            self::assertSame('API request sent', $handler->getLogs()[0]['message']);
            self::assertSame(
                \sprintf('Exception caught: %s', $previous === null ? '' : $previous->getMessage()),
                $handler->getLogs()[1]['message']
            );
            self::assertSame('API response received', $handler->getLogs()[2]['message']);

            // Return so the failure doesn't trigger, this will only trigger if exception isn't handled
            return;
        }

        self::fail('Expecting request exception but it was never thrown');
    }

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

        $this->createInstance(new MockHandler([new TransferException('An error occured')]))->request('get', 'test');
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

        $this->createInstance(new MockHandler([
            new RequestException('An error occured', new Request('GET', 'test'), new Response(500, [], 'error'))
        ]))->request('get', 'test');
    }

    /**
     * Test handling of request exception without response body
     *
     * @return void
     */
    public function testHandlingGuzzleRequestExceptionWithoutBody(): void
    {
        try {
            $this->createInstance(new MockHandler([
                new RequestException('An error occured', new Request('GET', 'test'))
            ]))->request('get', 'test');
        } catch (InvalidApiResponseException $exception) {
            self::assertSame('{"exception":"An error occured"}', $exception->getResponse()->getContent());
        }
    }

    /**
     * Test json response processing
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testJsonResponseProcessing(): void
    {
        $response = $this->createInstance(
            new MockHandler([new Response(200, [], '{"test":"1"}')])
        )->request('get', 'test');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"test":"1"}', $response->getContent());

        // Make sure data can be accessed
        self::assertSame('1', $response->get('test'));
    }

    /**
     * Test logger logs request and response
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testLoggerLogsRequestResponse(): void
    {
        $handler = new LogHandlerStub();

        // Send request
        $this->createInstance(
            new MockHandler([new Response(200, [], 'ok')]),
            new Logger(null, $handler)
        )->request('get', 'test');

        self::assertCount(2, $handler->getLogs());
        self::assertSame('API request sent', $handler->getLogs()[0]['message']);
        self::assertSame('API response received', $handler->getLogs()[1]['message']);
    }

    /**
     * Test processing a standard request
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testRequestProcessing(): void
    {
        $response = $this->createInstance(new MockHandler([new Response(200, [], 'ok')]))->request('get', 'test');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('ok', $response->getContent());
    }

    /**
     * Test xml response processing
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException If there is a request error
     */
    public function testXmlResponseProcessing(): void
    {
        $response = $this->createInstance(
            new MockHandler([new Response(200, [], '<?xml version="1.0"?><data><test>1</test></data>')])
        )->request('get', 'test');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<?xml version="1.0"?><data><test>1</test></data>', $response->getContent());

        // Make sure data can be accessed
        self::assertSame('1', $response->get('test'));
    }

    /**
     * Create client instance
     *
     * @param \GuzzleHttp\Handler\MockHandler $handler Guzzle mock handler
     * @param \EoneoPay\Externals\Logger\Interfaces\LoggerInterface|null $logger Logger instance to use
     *
     * @return \EoneoPay\Externals\HttpClient\Client
     */
    private function createInstance(MockHandler $handler, ?LoggerInterface $logger = null): Client
    {
        // Create guzzle with mock response
        return new Client(new Guzzle(['handler' => $handler]), $logger);
    }
}
