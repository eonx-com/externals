<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health;

use EoneoPay\Externals\Health\Health;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Tests\EoneoPay\Externals\TestCase;

class HealthTest extends TestCase
{
    /**
     * Tests that the 'extended' method returns positive health check result matching
     * the expected data.
     *
     * @return void
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
     * @return \EoneoPay\Externals\Health\Health
     */
    private function getInstance(array $checks): Health
    {
        return new Health($checks);
    }
}
