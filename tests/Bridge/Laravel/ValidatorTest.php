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
     * Test error messages work as expected.
     *
     * @return void
     */
    public function testValidatorWithFailedValidation(): void
    {
        $instance = $this->createValidator();
        $result = $instance->validate(['key' => 'value'], ['missing' => 'required']);

        self::assertSame(['missing' => ['missing is required']], $result);
    }

    /**
     * Test validator can validate data.
     *
     * @return void
     */
    public function testValidatorWithSuccessfulValidation(): void
    {
        $instance = $this->createValidator();
        $result = $instance->validate(['key' => 'value'], ['key' => 'required|string']);

        self::assertSame([], $result);
    }

    /**
     * Create validation instance.
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
