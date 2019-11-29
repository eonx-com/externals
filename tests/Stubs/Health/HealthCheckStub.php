<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Health;

use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;

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
     * @var int
     */
    private $state;

    /**
     * Constructs a new instance of the stub.
     *
     * @param string|null $name
     * @param string|null $shortName
     * @param int|null $state
     */
    public function __construct(
        ?string $name = null,
        ?string $shortName = null,
        ?int $state = null
    ) {
        $this->name = $name ?? 'Stubbed Health Check';
        $this->shortName = $shortName ?? 'stubbed';
        $this->state = $state;
    }

    /**
     * Performs a health check.
     *
     * @return int
     */
    public function check(): int
    {
        return $this->state;
    }

    /**
     * Gets the name of the health check.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the shortened name of the check.
     *
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }
}
