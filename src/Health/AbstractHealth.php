<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health;

use EoneoPay\Externals\Health\Interfaces\HealthInterface;

abstract class AbstractHealth implements HealthInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getChecks(): array;

    /**
     * {@inheritdoc}
     */
    public function extended(): array
    {
        // Get the classes to call upon for health checks
        $checks = $this->getChecks();

        // If there are no checks set, return early.
        if (\count($checks) === 0) {
            return [];
        }

        // Get the results of each check.
        $results = [];
        foreach ($checks as $check) {
            $results[$check->getShortName()] = $check->check();
        }

        // Return the results
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function simple(): int
    {
        return self::STATE_HEALTHY;
    }
}
