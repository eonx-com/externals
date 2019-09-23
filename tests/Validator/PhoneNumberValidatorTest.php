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
     * Returns test data for format function.
     *
     * @return mixed[]
     */
    public function getFormatData(): iterable
    {
        // input => output
        yield ['0312345678', '+61312345678'];
        yield ['0312345678', '+61312345678'];
        yield ['312345678', '+61312345678'];
        yield ['0400000000', '+61400000000'];
        yield ['1800123456', '+611800123456'];
        yield ['+61400000000', '+61400000000'];
        yield ['+1202-555-0191', '+12025550191'];

        // invalid numbers
        yield 'no area code' => ['12345678', null];
        yield 'not enough digits' => ['+6140000', null];
        yield ['email@example.net', null];
        yield ['elephant', null];
    }

    /**
     * Returns test data for the validate function.
     *
     * @return mixed[]
     */
    public function getValidityData(): iterable
    {
        // input => valid bool
        yield ['0312345678', true];
        yield ['312345678', true];
        yield ['0400000000', true];
        yield ['1800123456', true];
        yield ['+61400000000', true];
        yield ['+1202-555-0191', true];

        // invalid numbers
        yield 'no area code' => ['12345678', false];
        yield 'not enough digits' => ['+6140000', false];
        yield ['email@example.net', false];
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

        self::assertSame($valid, $result);
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

        self::assertSame($expected, $result);
    }
}
