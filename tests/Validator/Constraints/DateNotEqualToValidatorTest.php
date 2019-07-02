<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateNotEqualTo;
use EoneoPay\Externals\Validator\Constraints\DateNotEqualToValidator;
use Symfony\Component\Validator\Constraints\NotEqualToValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateNotEqualToValidator
 */
class DateNotEqualToValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a NotEqualTo validator
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateNotEqualTo([
            'value' => '2019-07-01T00:00:00Z'
        ]);

        $context = $this->buildContext($constraint);
        $inner = new NotEqualToValidator();
        $inner->initialize($context);

        $validator = new DateNotEqualToValidator($inner);

        $validator->validate('2018-04-05T12:34:55Z', $constraint);

        static::assertCount(0, $context->getViolations());
    }
}
