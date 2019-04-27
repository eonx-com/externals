<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\ExceptionHandler;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\ExceptionHandler
 */
class ExceptionHandlerTest extends TestCase
{
    /**
     * Tests the response when a non RequestException occurs
     *
     * @return void
     */
    public function testGetResponseForNonResponseException(): void
    {
        $instance = $this->createInstance();

        $result = $instance->getResponseFrom(new TransferException('message'));

        static::assertSame(500, $result->getStatusCode());
        static::assertSame('{"exception":"message"}', $result->getBody()->__toString());
    }

    /**
     * Tests the response when a RequestException occurs without a response
     *
     * @return void
     */
    public function testGetResponseForClientExceptionWithoutResponse(): void
    {
        $instance = $this->createInstance();

        $result = $instance->getResponseFrom(new ClientException('message', new Request('post', '/')));

        static::assertSame(400, $result->getStatusCode());
        static::assertSame('{"exception":"message"}', $result->getBody()->__toString());
    }

    /**
     * Tests the response when a RequestException occurs with a response
     *
     * @return void
     */
    public function testGetResponseForClientExceptionWithResponse(): void
    {
        $instance = $this->createInstance();

        $response = new Response(200);
        $result = $instance->getResponseFrom(new ClientException(
            'message',
            new Request('post', '/'),
            $response
        ));

        static::assertSame($response, $result);
    }

    /**
     * Creates an instance
     *
     * @return \EoneoPay\Externals\HttpClient\ExceptionHandler
     */
    private function createInstance(): ExceptionHandler
    {
        return new ExceptionHandler();
    }
}
