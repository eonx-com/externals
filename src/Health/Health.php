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
            return [
                'state' => self::STATE_HEALTHY,
                'services' => []
            ];
        }

        // Get the results of each check.
        $states = [];
        foreach ($this->checks as $check) {
            $states[$check->getShortName()] = $check->check();
        }

        // Supply an overall health
        $overall = (\array_search(self::STATE_DEGRADED, \array_values($states), true) > -1) === true
            ? self::STATE_DEGRADED
            : self::STATE_HEALTHY;

        // Return the results
        return [
            'state' => $overall,
            'services' => $states
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function simple(): int
    {
        return self::STATE_HEALTHY;
    }
}
