<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Health;

use EoneoPay\Externals\Health\AbstractHealth;

/**
 * @coversNothing
 */
class HealthStub extends AbstractHealth
{
    /**
     * @var \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[]
     */
    private $checks;

    /**
     * Constructs a new instance of the stub.
     *
     * @param \EoneoPay\Externals\Health\Interfaces\HealthCheckInterface[]|null $checks
     */
    public function __construct(?array $checks = null)
    {
        $this->checks = $checks ?? [
                new HealthCheckStub('Test Health Check', 'test-health-check')
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function getChecks(): array
    {
        return $this->checks;
    }
}
