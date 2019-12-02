<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health;

use EoneoPay\Externals\Health\AbstractHealth;
use EoneoPay\Externals\Health\Exceptions\InvalidClassInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use stdClass as stdClass;
use Tests\EoneoPay\Externals\Stubs\Health\HealthCheckStub;
use Tests\EoneoPay\Externals\Stubs\Health\HealthStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Health\AbstractHealth
 */
class HealthTest extends TestCase
{
    /**
     * Tests that the extended health check returns an empty result when no checks have
     * been passed in to the constructor of the service.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Health\Exceptions\InvalidClassInterface
     */
    public function testExtendedCheckReturnsEmptyResultWhenNoChecksPassed(): void
    {
        $instance = $this->getInstance([]);
        $expected = [];

        $result = $instance->extended();

        self::assertSame($expected, $result);
    }

    /**
     * Tests that the 'extended' method throws an exception when it comes across a provided check class
     * that does not implement the 'HealthCheckInterface' interface.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Health\Exceptions\InvalidClassInterface
     */
    public function testExtendedCheckThrowsExceptionWithInvalidClass(): void
    {
        $checks = [new stdClass()];
        $instance = $this->getInstance($checks);

        $this->expectException(InvalidClassInterface::class);
        $this->expectExceptionMessage('exceptions.health.invalid_class');

        $instance->extended();
    }

    /**
     * Tests that the 'extended' method returns positive health check result matching
     * the expected data.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Health\Exceptions\InvalidClassInterface
     */
    public function testExtendedCheckReturnsNegativeResult(): void
    {
        $instance = $this->getInstance([
            new HealthCheckStub(
                'Test Health Check',
                'test-health-check',
                HealthInterface::STATE_DEGRADED
            )
        ]);
        $expected = [
            'test-health-check' => HealthInterface::STATE_DEGRADED
        ];

        $result = $instance->extended();

        self::assertSame($expected, $result);
    }

    /**
     * Tests that the 'extended' method returns positive health check result matching
     * the expected data.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Health\Exceptions\InvalidClassInterface
     */
    public function testExtendedCheckReturnsPositiveResult(): void
    {
        $instance = $this->getInstance([
            new HealthCheckStub(
                'Test Health Check',
                'test-health-check',
                HealthInterface::STATE_HEALTHY
            )
        ]);
        $expected = [
            'test-health-check' => HealthInterface::STATE_HEALTHY
        ];

        $result = $instance->extended();

        self::assertSame($expected, $result);
    }

    /**
     * Tests that the combined checker's 'simple' method returns a positive value.
     *
     * @return void
     */
    public function testSimpleCheckReturnsPositiveResult(): void
    {
        $instance = $this->getInstance([]);

        $result = $instance->simple();

        self::assertSame(HealthInterface::STATE_HEALTHY, $result);
    }

    /**
     * Gets an instance of the health service.
     *
     * @param \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[] $checks
     *
     * @return \EoneoPay\Externals\Health\AbstractHealth
     */
    private function getInstance(array $checks): AbstractHealth
    {
        return new HealthStub($checks);
    }
}
