<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateComparison;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateComparison
 */
class DateComparisonTest extends TestCase
{
    /**
     * Test that the annotation targets classes
     *
     * @return void
     */
    public function testTarget(): void
    {
        $classConstant = 'property';
        $defaultProperty = 'expr';

        $constraint = new DateComparison();

        self::assertSame($defaultProperty, $constraint->getDefaultOption());
        self::assertSame($classConstant, $constraint->getTargets());
    }
}
