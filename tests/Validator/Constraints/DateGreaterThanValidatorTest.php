<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateGreaterThan;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanValidator;
use Symfony\Component\Validator\Constraints\GreaterThanValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateGreaterThanValidator
 */
class DateGreaterThanValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a GreaterThan validator.
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateGreaterThan([
            'value' => '2019-07-01T00:00:00Z',
        ]);

        $context = $this->buildContext($constraint);
        $inner = new GreaterThanValidator();
        $inner->initialize($context);

        $validator = new DateGreaterThanValidator($inner);

        $validator->validate('2020-04-05T12:34:55Z', $constraint);

        self::assertCount(0, $context->getViolations());
    }
}
