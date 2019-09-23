<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Externals\HttpClient\Exceptions\NetworkException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\Exceptions\NetworkException
 */
class NetworkExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes.
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $request = new Request('GET', '/');
        $exception = new NetworkException($request, new ConnectException('message', $request));

        self::assertSame(1100, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
        self::assertSame($request, $exception->getRequest());
    }
}
