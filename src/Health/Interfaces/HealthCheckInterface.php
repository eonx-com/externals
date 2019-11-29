<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Interfaces;

interface HealthCheckInterface
{
    /**
     * Performs a health check.
     *
     * @return int
     */
    public function check(): int;

    /**
     * Gets the name of the health check.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets the shortened name of the check.
     *
     * @return string
     */
    public function getShortName(): string;
}
