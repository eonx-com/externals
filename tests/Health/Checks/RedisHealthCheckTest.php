<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\RedisHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Illuminate\Contracts\Redis\Connector;
use Illuminate\Redis\RedisManager;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Redis\RedisClientStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Health\Checks\RedisHealthCheck
 */
class RedisHealthCheckTest extends TestCase
{
    /**
     * Test failed connection to redis. This is an example of not properly configured redis manager.
     *
     * @return void
     */
    public function testFailedConnection(): void
    {
        $redisManager = new RedisManager(
            new ApplicationStub(),
            'phpredis',
            []
        );

        $instance = new RedisHealthCheck($redisManager);
        $expected = new HealthState(
            HealthInterface::STATE_DEGRADED,
            'Redis connection failed.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }

    /**
     * Test successful connection to redis.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testSuccessfulConnection(): void
    {
        $redisManager = new RedisManager(
            new ApplicationStub(),
            'client_stub',
            [
                'default' => []
            ]
        );

        $redisManager->extend('client_stub', function (): Connector {
            return new RedisClientStub();
        });

        $instance = new RedisHealthCheck($redisManager);
        $expected = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'Redis connection successful.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }

    /**
     * Test getting names.
     *
     * @return void
     */
    public function testGettingNames(): void
    {
        $redisManager = new RedisManager(
            new ApplicationStub(),
            'phpredis',
            []
        );

        $instance = new RedisHealthCheck($redisManager);

        self::assertSame('redis', $instance->getShortName());
        self::assertSame('Redis', $instance->getName());
    }
}
