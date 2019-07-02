<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateEqualTo;
use EoneoPay\Externals\Validator\Constraints\DateEqualToValidator;
use Symfony\Component\Validator\Constraints\EqualToValidator;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateEqualToValidator
 */
class DateEqualToValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test that the validator wraps a EqualTo validator
     *
     * @return void
     */
    public function testValidationPassthrough(): void
    {
        $constraint = new DateEqualTo([
            'value' => '2019-07-01T00:00:00Z'
        ]);

        $context = $this->buildContext($constraint);
        $inner = new EqualToValidator();
        $inner->initialize($context);

        $validator = new DateEqualToValidator($inner);

        $validator->validate('2019-07-01T00:00:00Z', $constraint);

        static::assertCount(0, $context->getViolations());
    }
}
