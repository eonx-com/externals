<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Response;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\Response
 */
class ResponseTest extends TestCase
{
    /**
     * Test response object
     *
     * @return void
     */
    public function testResponseObject(): void
    {
        $psrResponse = new PsrResponse(200, ['Content-Type' => 'application/json'], '{"test":"1"}');
        $response = new Response($psrResponse, ['test' => '1']);

        self::assertSame('{"test":"1"}', $response->getContent());
        self::assertSame(['Content-Type' => ['application/json']], $response->getHeaders());
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('OK', $response->getReasonPhrase());

        // Test getting a single header
        self::assertSame(['application/json'], $response->getHeader('Content-Type'));

        // Test getting a single item
        self::assertSame('1', $response->get('test'));

        // Test status code result
        self::assertTrue($response->isSuccessful());

        $newResponse = $response->withStatus(204);

        self::assertSame(204, $newResponse->getStatusCode());
        self::assertSame('No Content', $newResponse->getReasonPhrase());

        $response = new Response(new PsrResponse(500));
        self::assertFalse($response->isSuccessful());
    }
}
