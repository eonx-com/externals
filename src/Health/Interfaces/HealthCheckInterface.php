<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Interfaces;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;

interface HealthCheckInterface
{
    /**
     * Performs a health check.
     *
     * @return \EoneoPay\Externals\DataTransferObjects\Health\HealthState
     */
    public function check(): HealthState;

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
