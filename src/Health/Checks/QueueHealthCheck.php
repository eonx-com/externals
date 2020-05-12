<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\Queue\Jobs\NullJob;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;

final class QueueHealthCheck implements HealthCheckInterface
{
    /**
     * The degraded state message.
     *
     * @const string
     */
    public const MESSAGE_DEGRADED = 'The dispatch queue service is degraded.';

    /**
     * The healthy state message.
     *
     * @const string
     */
    public const MESSAGE_HEALTHY = 'The dispatch queue service is healthy.';

    /**
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    private $dispatcher;

    /**
     * Constructs a new instance of the service.
     *
     * @param \Illuminate\Contracts\Bus\Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        $state = HealthInterface::STATE_DEGRADED;

        try {
            $this->dispatcher->dispatch(new NullJob());

            $state = HealthInterface::STATE_HEALTHY;
        } /** @noinspection BadExceptionsProcessingInspection */ catch (Exception $exception) {
            // If an exception occurs during dispatching, queue isnt available.
        }

        return new HealthState(
            $state,
            $state === HealthInterface::STATE_HEALTHY
                ? self::MESSAGE_HEALTHY
                : self::MESSAGE_DEGRADED
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Dispatch Queue';
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return 'queue';
    }
}
