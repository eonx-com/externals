<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Illuminate\Contracts\Redis\Factory;

final class RedisHealthCheck implements HealthCheckInterface
{
    /**
     * @var \Illuminate\Contracts\Redis\Factory
     */
    private $redis;

    /**
     * RedisHealthCheck constructor.
     *
     * @param \Illuminate\Contracts\Redis\Factory $redis
     */
    public function __construct(Factory $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        try {
            $this->redis->connection();
            // no exception is good enough for a healthy check.
            $state = HealthInterface::STATE_HEALTHY;
        } /** @noinspection BadExceptionsProcessingInspection */ catch (\Exception $exception) {
            // If exception is thrown and for whatever reason, the state is marked as degraded.
            $state = HealthInterface::STATE_DEGRADED;
        }

        return new HealthState(
            $state,
            $state === HealthInterface::STATE_HEALTHY
                ? 'Redis connection successful.'
                : 'Redis connection failed.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Redis';
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return 'redis';
    }
}
