<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Utils\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tests\EoneoPay\Externals\Stubs\Validator\Constraints\DateConstraintValidatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Validator\ConstraintValidatorStub;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\AbstractDateConstraintValidator
 */
class AbstractDateConstraintValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Tests that initialize calls the inner validator.
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $constraint = new NotBlank();

        $inner = new ConstraintValidatorStub();
        $validator = new DateConstraintValidatorStub($inner);

        $context = $this->buildContext($constraint);
        $validator->initialize($context);

        self::assertSame([$context], $inner->getInitialized());
    }

    /**
     * Test that the validator wraps a EqualTo validator.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testValidationPassthrough(): void
    {
        $value = '2019-07-01T00:00:00Z';
        $constraint = new NotBlank();

        $inner = new ConstraintValidatorStub();
        $validator = new DateConstraintValidatorStub($inner);

        $validator->validate($value, $constraint);

        self::assertCount(1, $inner->getValidated());
        $call = $inner->getValidated()[0];

        self::assertEquals(new DateTime('2019-07-01T00:00:00Z'), $call['value']);
        self::assertSame($constraint, $call['constraint']);
    }

    /**
     * Test null value does not call inner validator.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testNullValue(): void
    {
        $value = null;
        $constraint = new NotBlank();

        $inner = new ConstraintValidatorStub();
        $validator = new DateConstraintValidatorStub($inner);

        $validator->validate($value, $constraint);

        self::assertCount(0, $inner->getValidated());
    }

    /**
     * Test invalid date value does not call inner validator.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testInvalidDate(): void
    {
        $value = 'purple elephant';
        $constraint = new NotBlank();

        $inner = new ConstraintValidatorStub();
        $validator = new DateConstraintValidatorStub($inner);

        $validator->validate($value, $constraint);

        self::assertCount(0, $inner->getValidated());
    }
}
