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
     * Data provider for testing empty_with rule.
     *
     * @return mixed[]
     */
    public function getValidationData(): iterable
    {
        // If key1 has a value without key2 the rule should pass
        yield 'Pass key2 empty with key1 value' => [
            'data' => ['key1' => 'value1'],
            'result' => [],
            'rules' => ['key2' => 'empty_with:key1|string'],
        ];

        // If key1 has a value without key2 the rule should pass
        yield 'Pass key2 empty with no data' => [
            'data' => [],
            'result' => [],
            'rules' => ['key2' => 'empty_with:key1|string'],
        ];

        // If both keys are specified but only one has a value the rule should pass
        yield 'Pass key2 empty with key1 empty' => [
            'data' => ['key1' => 'value1', 'key2' => ''],
            'result' => [],
            'rules' => ['key2' => 'empty_with:key1|string'],
        ];

        // If just the tested key has a value the rule should pass
        yield 'Pass key1 empty with key2 value' => [
            'data' => ['key1' => '', 'key2' => 'value2'],
            'result' => [],
            'rules' => ['key2' => 'empty_with:key1|string'],
        ];

        // If both keys and value have values the rule should fail
        yield 'Fail key1 empty with key2 value' => [
            'data' => ['key1' => 'value1', 'key2' => 'value2'],
            'result' => ['key2' => ['key2 must be empty when key1 / key3 is present']],
            'rules' => ['key2' => 'empty_with:key1,key3|string'],
        ];
    }

    /**
     * Test custom rule to empty with.
     *
     * @param mixed[] $data
     * @param mixed[] $result
     * @param mixed[] $rules
     *
     * @return void
     *
     * @dataProvider getValidationData()
     */
    public function testValidatorCustomRuleEmptyWith(array $data, array $result, array $rules): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['empty_with' => ':attribute must be empty when :values is present']);

        $validator = new Validator(new Factory(new Translator($loader, 'en')));

        self::assertSame($result, $validator->validate($data, $rules));
    }
}
