<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\ExceptionHandler;
use EoneoPay\Externals\HttpClient\Exceptions\NetworkException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\ExceptionHandler
 */
class ExceptionHandlerTest extends TestCase
{
    /**
     * Tests that the handle method throws a ConnectException as a NetworkException.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\NetworkException
     */
    public function testHandleThrowsNetworkException(): void
    {
        $this->expectException(NetworkException::class);

        $request = new Request('POST', '');

        $instance = $this->getInstance();
        $instance->handle($request, new ConnectException('', $request));
    }

    /**
     * Tests that the exception handler adds a response when one isnt present.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\NetworkException
     */
    public function testHandlesRequestExceptionWithoutResponse(): void
    {
        $request = new Request('POST', '');
        $requestException = new RequestException('Something happened', $request);

        $instance = $this->getInstance();
        $response = $instance->handle($request, $requestException);

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('{"exception":"Something happened"}', $response->getBody()->__toString());
    }

    /**
     * Tests that the exception handler adds a response when one isnt present.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\NetworkException
     */
    public function testHandlesRequestExceptionWithResponse(): void
    {
        $request = new Request('POST', '');
        $response = new Response(500);
        $requestException = new RequestException('Something happened', $request, $response);

        $instance = $this->getInstance();
        $actual = $instance->handle($request, $requestException);

        self::assertSame($response, $actual);
    }

    /**
     * Creates an instance.
     *
     * @return \EoneoPay\Externals\HttpClient\ExceptionHandler
     */
    private function getInstance(): ExceptionHandler
    {
        return new ExceptionHandler();
    }
}
