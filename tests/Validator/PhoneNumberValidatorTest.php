<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator;

use EoneoPay\Externals\Validator\PhoneNumberValidator;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Validator\PhoneNumberValidator
 */
class PhoneNumberValidatorTest extends TestCase
{
    /**
     * Returns test data for format function
     *
     * @return mixed[]
     */
    public function getFormatData(): iterable
    {
        yield ['0400000000', '+61400000000'];
        yield ['+61400000000', '+61400000000'];
        yield ['elephant', null];
    }

    /**
     * Returns test data for the validate function.
     *
     * @return mixed[]
     */
    public function getValidityData(): iterable
    {
        yield ['0400000000', true];
        yield ['+61400000000', true];
        yield ['elephant', false];
    }

    /**
     * Tests numbers for validity.
     *
     * @param string $number
     * @param bool $valid
     *
     * @return void
     *
     * @dataProvider getValidityData
     */
    public function testValidate(string $number, bool $valid): void
    {
        $validator = new PhoneNumberValidator('AU');

        $result = $validator->validate($number);

        static::assertSame($valid, $result);
    }

    /**
     * Tests numbers for validity.
     *
     * @param string $number
     * @param string|null $expected
     *
     * @return void
     *
     * @dataProvider getFormatData
     */
    public function testFormat(string $number, ?string $expected): void
    {
        $validator = new PhoneNumberValidator('AU');

        $result = $validator->format($number);

        static::assertSame($expected, $result);
    }
}
