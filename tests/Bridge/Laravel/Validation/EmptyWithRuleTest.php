<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Validation;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validation\EmptyWithRule
 */
class EmptyWithRuleTest extends TestCase
{
    /**
     * Test custom rule to empty with
     *
     * @return void
     */
    public function testValidatorCustomRuleEmptyWith(): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['empty_with' => ':attribute must be empty when :values is present']);

        $validator = new Validator(new Factory(new Translator($loader, 'en')));

        // If key1 has a value without key2 the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => 'value1'],
            ['key2' => 'empty_with:key1|string']
        ));

        // If neither key has a value the rule should pass
        self::assertTrue($validator->validate(
            [],
            ['key2' => 'empty_with:key1|string']
        ));

        // If both keys are specified but only one has a value the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => 'value1', 'key2' => ''],
            ['key2' => 'empty_with:key1|string']
        ));

        // If just the tested key has a value the rule should pass
        self::assertTrue($validator->validate(
            ['key1' => '', 'key2' => 'value2'],
            ['key2' => 'empty_with:key1|string']
        ));

        // If both keys and value have values the rule should fail
        self::assertFalse($validator->validate(
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'empty_with:key1,key3|string']
        ));

        // Test failure message
        self::assertSame($validator->getFailures(), ['key2' => ['key2 must be empty when key1 / key3 is present']]);
    }
}
