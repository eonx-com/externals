<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Exceptions;

use EoneoPay\Externals\Health\Exceptions\InvalidClassInterface;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Health\Exceptions\InvalidClassInterface
 */
class InvalidClassInterfaceTest extends TestCase
{
    /**
     * Tests that the exception codes match the expected.
     *
     * @return void
     */
    public function testExceptionCodes(): void
    {
        $exception = new InvalidClassInterface();

        self::assertSame(1300, $exception->getErrorCode());
        self::assertSame(1, $exception->getErrorSubCode());
    }
}
