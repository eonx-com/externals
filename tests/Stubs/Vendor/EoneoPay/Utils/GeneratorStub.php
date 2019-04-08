<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\EoneoPay\Utils;

use EoneoPay\Utils\Interfaces\GeneratorInterface;

class GeneratorStub implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function randomInteger(?int $minimum = null, ?int $maximum = null): int
    {
        return 42;
    }

    /**
     * @inheritdoc
     */
    public function randomString(?int $length = null, ?int $flags = null): string
    {
        return 'notrandom';
    }
}
