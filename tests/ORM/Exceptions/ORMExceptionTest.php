<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Exceptions\ORMException
 */
class ORMExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes.
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $exception = new ORMException();

        self::assertSame(9000, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
        self::assertSame('A database error occurred', $exception->getErrorMessage());
    }
}
