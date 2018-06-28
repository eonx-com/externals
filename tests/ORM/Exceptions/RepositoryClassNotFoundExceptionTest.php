<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException
 */
class RepositoryClassNotFoundExceptionTest extends TestCase
{
    /**
     * Test exception returns the correct error codes
     *
     * @return void
     */
    public function testExceptionGetters(): void
    {
        $exception = new RepositoryClassNotFoundException();

        self::assertSame(500, $exception->getErrorCode());
        self::assertSame(0, $exception->getErrorSubCode());
    }
}
