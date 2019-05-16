<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * Test validated method after validation
     *
     * @return void
     */
    public function testValidatedAfterCallToValidate(): void
    {
        $data = ['key' => 'value', 'extra-key' => 'extra-key-value'];
        $rules = ['key' => 'required'];
        $validator = $this->createValidator();

        $validator->validate($data, $rules);
        $validated = $validator->validated($data, $rules);

        self::assertSame(['key' => 'value'], $validated);
    }

    /**
     * Test validated method return empty array if validation failed
     *
     * @return void
     */
    public function testValidatedWithFailedValidation(): void
    {
        $validator = $this->createValidator();

        $validated = $validator->validated(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required|integer']
        );

        self::assertSame([], $validated);
    }

    /**
     * Test validated method return correct values
     *
     * @return void
     */
    public function testValidatedWithSuccessfulValidation(): void
    {
        $validator = $this->createValidator();

        $validated = $validator->validated(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required']
        );

        self::assertSame(['key' => 'value'], $validated);
    }

    /**
     * Test error messages work as expected
     *
     * @return void
     */
    public function testValidatorWithFailedValidation(): void
    {
        $validator = $this->createValidator();

        self::assertFalse($validator->validate(['key' => 'value'], ['missing' => 'required']));
        self::assertSame($validator->getFailures(), ['missing' => ['missing is required']]);
    }

    /**
     * Test validator can validate data
     *
     * @return void
     */
    public function testValidatorWithSuccessfulValidation(): void
    {
        self::assertTrue($this->createValidator()->validate(['key' => 'value'], ['key' => 'required|string']));
    }

    /**
     * Create validation instance
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Validator
     */
    private function createValidator(): Validator
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['required' => ':attribute is required']);

        return new Validator(new Factory(new Translator($loader, 'en')));
    }
}
