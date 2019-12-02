<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Interfaces;

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
     * @return int[]
     */
    public function extended(): array;

    /**
     * Performs a simple health check.
     *
     * @return int
     */
    public function simple(): int;
}
