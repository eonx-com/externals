<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\DataTransferObjects;

use EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult;
use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult
 */
class HealthExtendedCheckResultTest extends TestCase
{
    /**
     * Tests the creation of the DTO ensuring that the values returned by the getters that those passed in to the
     * constructor.
     *
     * @return void
     */
    public function testCreation(): void
    {
        $state = new HealthState(HealthInterface::STATE_DEGRADED, 'Message');
        $instance = new HealthExtendedCheckResult(
            HealthInterface::STATE_DEGRADED,
            [$state]
        );

        self::assertSame(HealthInterface::STATE_DEGRADED, $instance->getState());
        self::assertCount(1, $instance->getServices());
        self::assertContains($state, $instance->getServices());
    }
}
