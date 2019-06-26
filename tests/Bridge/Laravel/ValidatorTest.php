<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use Closure;
use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use ReflectionClass;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * Test addCustomRule method.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testAddCustomRule(): void
    {
        $class = new ReflectionClass(Validator::class);
        $property = $class->getProperty('customRules');
        $property->setAccessible(true);
        $validator = $this->createValidator();

        $validator->addCustomRule('customRuleClassName1');
        $validator->addCustomRule('customRuleClassName2');

        self::assertEquals(['customRuleClassName1', 'customRuleClassName2'], $property->getValue($validator));
    }

    /**
     * Test validate method adds custom rules to the underlying validator.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testValidateAddsCustomRules(): void
    {
        $class = new ReflectionClass(Validator::class);
        $property = $class->getProperty('validator');
        $property->setAccessible(true);
        $validator = $this->createValidator();
        $customRule = $this->getCustomRuleClassDeclaration();
        $validator->addCustomRule(\get_class($customRule));

        $validator->validate(['key' => 'value'], ['key' => 'required|string']);

        /** @var \Illuminate\Validation\Validator $laravelValidator */
        $laravelValidator = $property->getValue($validator);
        self::assertArrayHasKey('empty_with', $laravelValidator->extensions);
        self::assertArrayHasKey('instance_of', $laravelValidator->extensions);
        self::assertArrayHasKey('custom_rule_name', $laravelValidator->extensions);
    }

    /**
     * Test validatedData method return empty array if validation failed
     *
     * @return void
     */
    public function testValidatedDataWithFailedValidation(): void
    {
        $validator = $this->createValidator();

        $validatedData = $validator->validatedData(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required|integer']
        );

        self::assertSame([], $validatedData);
        self::assertSame(['key' => ['validation.integer']], $validator->getFailures());
    }

    /**
     * Test validatedData method return correct values
     *
     * @return void
     */
    public function testValidatedDataWithSuccessfulValidation(): void
    {
        $validator = $this->createValidator();

        $validatedData = $validator->validatedData(
            ['key' => 'value', 'extra-key' => 'extra-key-value'],
            ['key' => 'required']
        );

        self::assertSame(['key' => 'value'], $validatedData);
        self::assertSame([], $validator->getFailures());
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
     * Returns custom validation rule anonymous class.
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface
     */
    protected function getCustomRuleClassDeclaration(): ValidationRuleInterface
    {
        $customRule = new class() implements ValidationRuleInterface {
            /**
             * Get rule name.
             *
             * @return string
             */
            public function getName(): string
            {
                return 'custom_rule_name';
            }

            /**
             * Get message replacements.
             *
             * @return \Closure
             */
            public function getReplacements(): Closure
            {
                return function () {
                };
            }

            /**
             * Get the validation rule closure.
             *
             * @return \Closure
             */
            public function getRule(): Closure
            {
                return function () {
                };
            }
        };

        return $customRule;
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
