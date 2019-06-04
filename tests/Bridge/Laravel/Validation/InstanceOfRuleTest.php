<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Validation;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validation\InstanceOfRule
 */
class InstanceOfRuleTest extends TestCase
{
    /**
     * Test custom rule to empty with
     *
     * @return void
     */
    public function testValidatorCustomRuleEmptyWith(): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['instance_of' => ':attribute must be an instance of :values']);

        $validator = new Validator(new Factory(new Translator($loader, 'en')));

        // If key1 is an instance of expected object rule should pass
        self::assertTrue($validator->validate(
            ['key1' => new \stdClass()],
            ['key1' => 'instance_of:' . \stdClass::class]
        ));

        // If key1 is not an instance of expected object rule should fail
        self::assertFalse($validator->validate(
            ['key1' => new \stdClass()],
            ['key1' => 'instance_of:' . EntityInterface::class]
        ));

        // Rule should take in consideration all parameters
        self::assertTrue($validator->validate(
            ['key1' => new \stdClass()],
            ['key1' => \sprintf('instance_of:%s,%s', EntityInterface::class, \stdClass::class)]
        ));

        // If no value provided rule should pass
        self::assertTrue($validator->validate(
            ['key1' => null],
            ['key1' => 'instance_of:' . \stdClass::class]
        ));

        // If no parameter provided rule should fail
        self::assertFalse($validator->validate(
            ['key1' => new \stdClass()],
            ['key1' => 'instance_of']
        ));

        // Test failure message
        self::assertSame($validator->getFailures(), ['key1' => ['key1 must be an instance of {NO PARAMETER}']]);
    }
}
