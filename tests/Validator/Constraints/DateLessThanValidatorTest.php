<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateLessThan;
use EoneoPay\Externals\Validator\Constraints\DateLessThanValidator;
use Symfony\Component\Validator\Constraints\LessThanValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateLessThanValidator
 */
class DateLessThanValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a LessThan validator.
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateLessThan([
            'value' => '2019-07-01T00:00:00Z',
        ]);

        $context = $this->buildContext($constraint);
        $inner = new LessThanValidator();
        $inner->initialize($context);

        $validator = new DateLessThanValidator($inner);

        $validator->validate('2018-04-05T12:34:55Z', $constraint);

        self::assertCount(0, $context->getViolations());
    }
}
