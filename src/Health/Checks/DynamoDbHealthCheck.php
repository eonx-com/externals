<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Exception;
use LoyaltyCorp\Auditing\Interfaces\ManagerInterface;

final class DynamoDbHealthCheck implements HealthCheckInterface
{
    /**
     * The degraded state message.
     *
     * @const string
     */
    public const MESSAGE_DEGRADED = 'The dynamo database service is degraded.';

    /**
     * The healthy state message.
     *
     * @const string
     */
    public const MESSAGE_HEALTHY = 'The dynamo database service is healthy.';

    /**
     * @var \LoyaltyCorp\Auditing\Interfaces\ManagerInterface
     */
    private $manager;

    /**
     * DynamoDbHealthCheck constructor.
     *
     * @param \LoyaltyCorp\Auditing\Interfaces\ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        $state = HealthInterface::STATE_DEGRADED;

        try {
            // A non-200 response code results in an Exception, hence why the results do not matter
            $this->manager->getDbClient()->listTables();

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
        return 'Connect to DynamoDb';
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return 'DynamoDb';
    }
}
