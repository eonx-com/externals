<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Validation;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use stdClass;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validation\InstanceOfRule
 */
class InstanceOfRuleTest extends TestCase
{
    /**
     * Data provider for testing empty_with rule.
     *
     * @return mixed[]
     */
    public function getValidationData(): iterable
    {
        // If key1 is an instance of expected object rule should pass
        yield 'Pass key1 contains correct instance' => [
            'data' => ['key1' => new stdClass()],
            'result' => [],
            'rules' => ['key1' => 'instance_of:' . stdClass::class],
        ];

        // If key1 is not an instance of expected object rule should fail
        yield 'Fail key1 incorrect instance' => [
            'data' => ['key1' => new stdClass()],
            'result' => ['key1' => [\sprintf('key1 must be an instance of %s', EntityInterface::class)]],
            'rules' => ['key1' => 'instance_of:' . EntityInterface::class],
        ];

        // Rule should take in consideration all parameters
        yield 'Pass key1 contains instance from list' => [
            'data' => ['key1' => new stdClass()],
            'result' => [],
            'rules' => ['key1' => \sprintf('instance_of:%s,%s', EntityInterface::class, stdClass::class)],
        ];

        // If no value provided rule should pass
        yield 'Pass key1 is null' => [
            'data' => ['key1' => null],
            'result' => [],
            'rules' => ['key1' => 'instance_of:' . stdClass::class],
        ];

        // If no parameter provided rule should fail
        yield 'Fail parameter for instance missing' => [
            'data' => ['key1' => new stdClass()],
            'result' => ['key1' => ['key1 must be an instance of {NO PARAMETER}']],
            'rules' => ['key1' => 'instance_of'],
        ];
    }

    /**
     * Test custom rule to instance of.
     *
     * @param mixed[] $data
     * @param mixed[] $result
     * @param mixed[] $rules
     *
     * @return void
     *
     * @dataProvider getValidationData()
     */
    public function testValidatorCustomRuleInstanceOf(array $data, array $result, array $rules): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['instance_of' => ':attribute must be an instance of :values']);

        $validator = new Validator(new Factory(new Translator($loader, 'en')));

        self::assertSame($result, $validator->validate($data, $rules));
    }
}
