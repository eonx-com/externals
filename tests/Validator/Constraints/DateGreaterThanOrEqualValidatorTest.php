<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateGreaterThanOrEqual;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanOrEqualValidator;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateGreaterThanOrEqualValidator
 */
class DateGreaterThanOrEqualValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a GreaterThanOrEqual validator
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateGreaterThanOrEqual([
            'value' => '2019-07-01T00:00:00Z'
        ]);

        $context = $this->buildContext($constraint);
        $inner = new GreaterThanOrEqualValidator();
        $inner->initialize($context);

        $validator = new DateGreaterThanOrEqualValidator($inner);

        $validator->validate('2020-04-05T12:34:55Z', $constraint);

        static::assertCount(0, $context->getViolations());
    }
}
