<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Bridge\Laravel\Request;
use Illuminate\Http\Request as HttpRequest;
use Tests\EoneoPay\External\TestCase;

/**
 * @covers \EoneoPay\External\Bridge\Laravel\Request
 */
class RequestTest extends TestCase
{
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
    }
}
