<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Health;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;

/**
 * @coversNothing
 */
class HealthCheckStub implements HealthCheckInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var \EoneoPay\Externals\DataTransferObjects\Health\HealthState
     */
    private $state;

    /**
     * Constructs a new instance of the stub.
     *
     * @param string|null $name
     * @param string|null $shortName
     * @param \EoneoPay\Externals\DataTransferObjects\Health\HealthState|null $state
     */
    public function __construct(
        ?string $name = null,
        ?string $shortName = null,
        ?HealthState $state = null
    ) {
        $this->name = $name ?? 'Stubbed Health Check';
        $this->shortName = $shortName ?? 'stubbed';
        $this->state = $state ?? new HealthState(HealthInterface::STATE_HEALTHY);
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }
}
