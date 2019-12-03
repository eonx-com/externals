<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health;

use EoneoPay\Externals\DataTransferObjects\Health\HealthExtendedCheckResult;
use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
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
    public function extended(): HealthExtendedCheckResult
    {
        // If there are no checks set, return early.
        if (\count($this->checks) === 0) {
            return new HealthExtendedCheckResult(
                self::STATE_HEALTHY,
                []
            );
        }

        // Get the results of each check.
        $states = [];
        foreach ($this->checks as $check) {
            $states[$check->getShortName()] = $check->check();
        }

        // Supply an overall health
        $numDegraded = \count(\array_filter($states, static function (HealthState $state): bool {
            // Filter each state and only return the states that are degraded
            return $state->getState() === HealthInterface::STATE_DEGRADED;
        }));
        $overall = $numDegraded > 0 ? self::STATE_DEGRADED : self::STATE_HEALTHY;

        // Return the results
        return new HealthExtendedCheckResult($overall, $states);
    }

    /**
     * {@inheritdoc}
     */
    public function simple(): int
    {
        return self::STATE_HEALTHY;
    }
}
