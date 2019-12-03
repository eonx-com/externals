<?php
declare(strict_types=1);

namespace EoneoPay\Externals\DataTransferObjects\Health;

use EoneoPay\Externals\Health\Interfaces\HealthInterface;

/**
 * A structured DTO for service health states.
 */
class HealthState
{
    /**
     * The default message for a degreaded serivce.
     *
     * @const string
     */
    public const DEFAULT_DEGRADED_MESSAGE = 'The service is degraded.';

    /**
     * The default message for a healthy service.
     *
     * @const string
     */
    public const DEFAULT_HEALTHY_MESSSAGE = 'The service is healthy.';

    /**
     * A brief description of the state.
     *
     * @var string|null
     */
    private $message;

    /**
     * The health state.
     *
     * @var int
     */
    private $state;

    /**
     * Constructs a new instance of the DTO.
     *
     * @param int $state The health state.
     * @param string|null $message The health state message.
     */
    public function __construct(int $state, ?string $message = null)
    {
        $this->state = $state;
        $this->message = $message;
    }

    /**
     * Gets the health state message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        if (\is_string($this->message) === false) {
            return ($this->state === HealthInterface::STATE_HEALTHY)
                ? self::DEFAULT_HEALTHY_MESSSAGE
                : self::DEFAULT_DEGRADED_MESSAGE;
        }

        return $this->message;
    }

    /**
     * Gets the health state.
     *
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }
}
