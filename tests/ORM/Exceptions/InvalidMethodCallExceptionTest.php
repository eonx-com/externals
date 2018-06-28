<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
 */
class InvalidMethodCallExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $exception = new InvalidMethodCallException();

        self::assertSame(1000, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
    }
}
