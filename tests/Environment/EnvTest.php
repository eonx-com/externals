<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Environment;

use EoneoPay\Externals\Environment\Env;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Environment\Env
 */
class EnvTest extends TestCase
{
    /**
     * Test getting a value from the environment.
     *
     * @return void
     */
    public function testEnvGetReturnedEnvironmentValue(): void
    {
        $env = new Env();

        // Test values set in phpunit.xml
        self::assertSame('testing', $env->get('ENV_TEST1'));
        self::assertSame('testing', $env->get('ENV_TEST2'));
        self::assertEmpty($env->get('ENV_EMPTY1'));
        self::assertEmpty($env->get('ENV_EMPTY2'));
        self::assertFalse($env->get('ENV_FALSE1'));
        self::assertFalse($env->get('ENV_FALSE2'));
        self::assertNull($env->get('ENV_NULL1'));
        self::assertNull($env->get('ENV_NULL2'));
        self::assertTrue($env->get('ENV_TRUE1'));
        self::assertTrue($env->get('ENV_TRUE2'));

        // Test default is returned if key doesn't exist
        self::assertNull($env->get('ENV_INVALID'));
        self::assertSame(1, $env->get('ENV_INVALID', static function (): int {
            return 1;
        }));
    }

    /**
     * Test setting and removing values to/from environment.
     *
     * @return void
     */
    public function testEnvSetAddsValueToEnvironment(): void
    {
        $env = new Env();

        // Only scalar and null values should be set
        self::assertFalse($env->set('TEST_ENV_ARRAY', ['testing' => 123]));

        // Test setting a value to the environment
        self::assertNull($env->get('TEST_ENV_INTEGER'));
        $env->set('TEST_ENV_INTEGER', 123);
        self::assertSame('123', $env->get('TEST_ENV_INTEGER'));

        // Remove from environment
        self::assertTrue($env->remove('TEST_ENV_INTEGER'));
        self::assertNull($env->get('TEST_ENV_INTEGER'));
    }
}
