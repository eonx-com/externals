<?php
declare(strict_types=1);

namespace EoneoPay\Externals\DataTransferObjects\Health;

/**
 * A structured DTO for extended health check results.
 */
class HealthExtendedCheckResult
{
    /**
     * @var \EoneoPay\Externals\DataTransferObjects\Health\HealthState[]
     */
    private $services;

    /**
     * The overall health state.
     *
     * @var int
     */
    private $state;

    /**
     * Constructs a new instance of the DTO.
     *
     * @param int $state The overall health state.
     * @param \EoneoPay\Externals\DataTransferObjects\Health\HealthState[] $services An array of health states.
     */
    public function __construct(int $state, array $services)
    {
        $this->state = $state;
        $this->services = $services;
    }

    /**
     * Gets the health states of each service.
     *
     * @return \EoneoPay\Externals\DataTransferObjects\Health\HealthState[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Gets the overall health state.
     *
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }
}
