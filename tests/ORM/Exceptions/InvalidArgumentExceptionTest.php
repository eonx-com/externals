<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $exception = new InvalidArgumentException();

        self::assertSame(1100, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
    }
}
