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
     * Test request can retrieve headers as expected
     *
     * @return void
     */
    public function testRequestReadsHeaderInformationFromServer(): void
    {
        $request = new Request(
            new HttpRequest([], [], [], [], [], ['HTTP_ACCEPT' => 'text/test', 'PHP_AUTH_USER' => 'user'])
        );

        self::assertSame('text/test', $request->getHeader('accept'));
        self::assertSame('text/test', $request->getHeader('Accept'));
        self::assertSame('default', $request->getHeader('invalid', 'default'));

        self::assertNull($request->getHeader('empty'));
        $request->setHeader('empty', 'notempty');
        self::assertSame('notempty', $request->getHeader('empty'));

        self::assertSame('user', $request->getUser());
    }

    /**
     * Test request works as expected
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
     * Test replacing data in the request
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
