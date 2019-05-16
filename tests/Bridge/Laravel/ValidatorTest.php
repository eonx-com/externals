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
     * Test validated method before validation. Logic exception should be thrown
     *
     * @return void
     */
    public function testValidatedBeforeCallToValidate(): void
    {
        $validator = $this->createValidator();

        $this->expectException(\LogicException::class);

        $validator->validated();
    }

    /**
     * Test validated method return empty array if validation failed
     *
     * @return void
     */
    public function testValidatedWithFailedValidation(): void
    {
        $validator = $this->createValidator();
        $validator->validate(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required|integer']
        );

        $validated = $validator->validated();

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
        $validator->validate(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required']
        );

        $validated = $validator->validated();

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
