<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\External\TestCase;

/**
 * @covers \EoneoPay\External\Bridge\Laravel\Validator
 */
class ValidatorTest extends TestCase
{
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
     * Create validation instance
     *
     * @return \EoneoPay\External\Bridge\Laravel\Validator
     */
    private function createValidator(): Validator
    {
        $messages = [
            'required' => ':attribute is required'
        ];

        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', $messages);

        return new Validator(new Factory(new Translator($loader, 'en')));
    }
}
