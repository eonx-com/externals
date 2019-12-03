<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Interfaces;

use EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult;

interface HealthInterface
{
    /**
     * Represents a degraded state.
     *
     * @const int
     */
    public const STATE_DEGRADED = 0;

    /**
     * Represents a healthy state.
     *
     * @const int
     */
    public const STATE_HEALTHY = 100;

    /**
     * Performs an extended health check.
     *
     * @return \EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult
     */
    public function extended(): HealthExtendedCheckResult;

    /**
     * Performs a simple health check.
     *
     * @return int
     */
    public function simple(): int;
}
