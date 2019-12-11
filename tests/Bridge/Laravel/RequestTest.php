<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Request;
use Illuminate\Http\Request as HttpRequest;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Request
 */
class RequestTest extends TestCase
{
    /**
     * Test client ip is read from header.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Trusted proxies must be set statically
     */
    public function testRequestPassesClientIp(): void
    {
        $request = new Request(new HttpRequest());
        self::assertNull($request->getClientIp());

        $request = new Request(new HttpRequest([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']));
        self::assertSame('127.0.0.1', $request->getClientIp());

        // Test proxy header is ignored if proxy isn't trusted
        HttpRequest::setTrustedProxies(['10.0.0.0/24'], HttpRequest::HEADER_X_FORWARDED_ALL);
        $request = new Request(new HttpRequest([], [], [], [], [], [
            'HTTP_X_FORWARDED_FOR' => '192.168.10.10',
            'REMOTE_ADDR' => '127.0.0.1',
        ]));
        self::assertSame('127.0.0.1', $request->getClientIp());

        // Test reading from proxy works if proxy is set
        HttpRequest::setTrustedProxies(['127.0.0.0/24'], HttpRequest::HEADER_X_FORWARDED_ALL);
        $request = new Request(new HttpRequest([], [], [], [], [], [
            'HTTP_X_FORWARDED_FOR' => '192.168.10.10',
            'REMOTE_ADDR' => '127.0.0.1',
        ]));
        self::assertSame('192.168.10.10', $request->getClientIp());
    }

    /**
     * Test request can retrieve headers as expected.
     *
     * @return void
     */
    public function testRequestReadsHeaderInformationFromServer(): void
    {
        $request = new Request(
            new HttpRequest(
                [],
                [],
                [],
                [],
                [],
                ['HTTP_ACCEPT' => 'text/test', 'HTTP_HOST' => 'localhost', 'PHP_AUTH_USER' => 'user']
            )
        );

        self::assertSame('text/test', $request->getHeader('accept'));
        self::assertSame('text/test', $request->getHeader('Accept'));
        self::assertSame('default', $request->getHeader('invalid', 'default'));

        self::assertNull($request->getHeader('empty'));
        $request->setHeader('empty', 'notempty');
        self::assertSame('notempty', $request->getHeader('empty'));

        self::assertSame('user', $request->getUser());
        self::assertSame('localhost', $request->getHost());
    }

    /**
     * Test request works as expected.
     *
     * @return void
     */
    public function testRequestReadsIncomingData(): void
    {
        $request = new Request(new HttpRequest(['key' => 'value']));

        self::assertTrue($request->has('key'));
        self::assertFalse($request->has('invalid'));
        self::assertSame('value', $request->input('key'));
        self::assertNull($request->input('invalid'));
        self::assertSame(['key' => 'value'], $request->toArray());
        self::assertSame('test', $request->input('missing', 'test'));
    }

    /**
     * Test replacing data in the request.
     *
     * @return void
     */
    public function testRequestReplaceAndMergeReplacesDataInTheRequest(): void
    {
        $request = new Request(new HttpRequest(['key' => 'value']));

        self::assertSame('value', $request->input('key'));

        $request->replace(['new' => 'data']);
        self::assertNull($request->input('key'));
        self::assertSame('data', $request->input('new'));

        self::assertNull($request->input('merged'));
        $request->merge(['merged' => 'value']);
        self::assertSame('value', $request->input('merged'));
        self::assertSame('data', $request->input('new'));
    }
}
