<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Response;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
 */
class InvalidApiResponseExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $response = new Response();
        $exception = new InvalidApiResponseException($response);

        self::assertSame(1100, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
        self::assertSame($response, $exception->getResponse());
    }
}
