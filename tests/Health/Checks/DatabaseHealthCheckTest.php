<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\DatabaseHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\TroubledEntityManagerStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\Health\Checks\DatabaseHealthCheck
 */
class DatabaseHealthCheckTest extends ORMTestCase
{
    /**
     * Test a degraded status is returned for a troubled entity manager.
     *
     * @return void
     */
    public function testDegradedStatus(): void
    {
        $entityManager = new TroubledEntityManagerStub();
        $instance = new DatabaseHealthCheck($entityManager);

        $expected = new HealthState(
            HealthInterface::STATE_DEGRADED,
            'The database connection is degraded.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }

    /**
     * Test names for health check service.
     *
     * @return void
     */
    public function testGetName(): void
    {
        $entityManager = $this->getDoctrineEntityManager();
        $instance = new DatabaseHealthCheck($entityManager);

        self::assertSame('Database', $instance->getName());
        self::assertSame('database', $instance->getShortName());
    }

    /**
     * Test a healthy status is returned for a good entity manager.
     *
     * @return void
     */
    public function testHealthyStatus(): void
    {
        /**
         * Get a healthy instance of doctrine entity manager which has setup Health entity namespace.
         */
        $entityManager = $this->getDoctrineEntityManager();
        $instance = new DatabaseHealthCheck($entityManager);

        $expected = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'The database connection is healthy.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }
}
