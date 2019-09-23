<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\Filter;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\Filter
 */
class FilterTest extends TestCase
{
    /**
     * Test that the annotation targets classes.
     *
     * @return void
     */
    public function testTarget(): void
    {
        $classConstant = 'property';
        $constraint = new Filter();

        self::assertSame($classConstant, $constraint->getTargets());
    }
}
