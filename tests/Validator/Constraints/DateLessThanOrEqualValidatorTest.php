<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateLessThanOrEqual;
use EoneoPay\Externals\Validator\Constraints\DateLessThanOrEqualValidator;
use Symfony\Component\Validator\Constraints\LessThanOrEqualValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateLessThanOrEqualValidator
 */
class DateLessThanOrEqualValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a LessThanOrEqual validator
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateLessThanOrEqual([
            'value' => '2019-07-01T00:00:00Z'
        ]);

        $context = $this->buildContext($constraint);
        $inner = new LessThanOrEqualValidator();
        $inner->initialize($context);

        $validator = new DateLessThanOrEqualValidator($inner);

        $validator->validate('2018-04-05T12:34:55Z', $constraint);

        static::assertCount(0, $context->getViolations());
    }
}
