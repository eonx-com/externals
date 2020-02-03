<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Exception;

final class DatabaseHealthCheck implements HealthCheckInterface
{
    /**
     * The degraded state message.
     *
     * @const string
     */
    public const MESSAGE_DEGRADED = 'The database connection is degraded.';

    /**
     * The healthy state message.
     *
     * @const string
     */
    public const MESSAGE_HEALTHY = 'The database connection is healthy.';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * DatabaseHealthCheck constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        $state = HealthInterface::STATE_HEALTHY;

        try {
            $this->entityManager->getConnection()->executeQuery('SELECT true');
        } /** @noinspection BadExceptionsProcessingInspection */ catch (DBALException $exception) {
            // If exception is thrown database is not readable, ignore this and set the state to degraded
            $state = HealthInterface::STATE_DEGRADED;
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
        return 'Database';
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return 'database';
    }
}
