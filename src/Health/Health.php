<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health;

use EoneoPay\Externals\Health\Interfaces\HealthInterface;

class Health implements HealthInterface
{
    /**
     * The health checks to perform.
     *
     * @var \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[]
     */
    private $checks;

    /**
     * Constructs a new instance of Health.
     *
     * @param \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[] $checks
     */
    public function __construct(array $checks)
    {
        $this->checks = $checks;
    }

    /**
     * {@inheritdoc}
     */
    public function extended(): array
    {
        // If there are no checks set, return early.
        if (\count($this->checks) === 0) {
            return [];
        }

        // Get the results of each check.
        $results = [];
        foreach ($this->checks as $check) {
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
