<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\LoggingClient;
use EoneoPay\Externals\Logger\Logger;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler\LogHandlerStub;
use Tests\EoneoPay\Externals\TestCase;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @covers \EoneoPay\Externals\HttpClient\LoggingClient
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test
 */
class LoggingClientTest extends TestCase
{
    /**
     * Tests logging when request fails
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testLogRequestFailure(): void
    {
        $handler = new MockHandler([
            $expectedException = new RequestException(
                'Request Exception',
                new Request('GET', '/'),
                new Response(500, [], 'error')
            )
        ]);
        $logger = new LogHandlerStub();

        $instance = $this->createInstance($handler, $logger);

        $previous = null;
        try {
            $instance->request('GET', '/');
        } catch (InvalidApiResponseException $exception) {
            $previous = $exception->getPrevious();
        }

        static::assertSame($expectedException, $previous);

        $logs = $logger->getLogs();

        static::assertCount(3, $logs);

        static::assertSame('HTTP Request Sent', $logs[0]['message']);
        static::assertSame('HTTP Response Received', $logs[1]['message']);
        static::assertSame('Exception caught: Request Exception', $logs[2]['message']);
    }

    /**
     * Tests logging when a successful request is made
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testLogRequestSuccess(): void
    {
        $handler = new MockHandler([
            new Response(200, [], stream_for('{"test": 1}'))
        ]);
        $logger = new LogHandlerStub();

        $instance = $this->createInstance($handler, $logger);

        $instance->request('GET', '/test');

        $logs = $logger->getLogs();

        static::assertCount(2, $logs);

        static::assertSame('HTTP Request Sent', $logs[0]['message']);
        static::assertSame('/test', $logs[0]['context']['uri']);
        static::assertSame('HTTP Response Received', $logs[1]['message']);
        static::assertSame('/test', $logs[1]['context']['uri']);
    }

    /**
     * Tests logging when request fails
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testSendRequestLogRequestFailure(): void
    {
        $handler = new MockHandler([
            $expectedException = new RequestException(
                'Request Exception',
                new Request('GET', '/test'),
                new Response(500, [], 'error')
            )
        ]);
        $logger = new LogHandlerStub();

        $instance = $this->createInstance($handler, $logger);

        $previous = null;
        try {
            $instance->sendRequest(new Request('get', '/test'));
        } catch (InvalidApiResponseException $exception) {
            $previous = $exception->getPrevious();
        }

        static::assertSame($expectedException, $previous);

        $logs = $logger->getLogs();

        static::assertCount(3, $logs);

        static::assertSame('HTTP Request Sent', $logs[0]['message']);
        static::assertSame('HTTP Response Received', $logs[1]['message']);
        static::assertSame('Exception caught: Request Exception', $logs[2]['message']);
    }

    /**
     * Tests logging when a successful request is made
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testSendRequestLogRequestSuccess(): void
    {
        $handler = new MockHandler([
            new Response(200, [], stream_for('{"test": 1}'))
        ]);
        $logger = new LogHandlerStub();

        $instance = $this->createInstance($handler, $logger);

        $instance->sendRequest(new Request('get', '/test'));

        $logs = $logger->getLogs();

        static::assertCount(2, $logs);

        static::assertSame('HTTP Request Sent', $logs[0]['message']);
        static::assertSame('/test', $logs[0]['context']['uri']);
        static::assertSame('HTTP Response Received', $logs[1]['message']);
        static::assertSame('/test', $logs[1]['context']['uri']);
    }

    /**
     * Creates an instance
     *
     * @param \GuzzleHttp\Handler\MockHandler $handler
     * @param \Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler\LogHandlerStub $logHandlerStub
     *
     * @return \EoneoPay\Externals\HttpClient\LoggingClient
     */
    private function createInstance(MockHandler $handler, LogHandlerStub $logHandlerStub): LoggingClient
    {
        return new LoggingClient(
            new Client(
                new \GuzzleHttp\Client(['handler' => $handler]),
                new ExceptionHandler()
            ),
            new Logger(null, $logHandlerStub)
        );
    }
}
