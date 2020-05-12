<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\QueueHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Eonx\TestUtils\TestCases\UnitTestCase;
use Illuminate\Contracts\Bus\Dispatcher;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Bus\DispatcherStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Bus\ThrowingDispatcherStub;

/**
 * @covers \EoneoPay\Externals\Health\Checks\QueueHealthCheck
 */
class QueueHealthCheckTest extends UnitTestCase
{
    /**
     * Test that the check method returns a degraded state.
     *
     * @return void
     */
    public function testCheckReturnsDegraded(): void
    {
        $dispatcher = new ThrowingDispatcherStub();
        $instance = $this->getInstance($dispatcher);
        $expected = new HealthState(
            HealthInterface::STATE_DEGRADED,
            'The dispatch queue service is degraded.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }

    /**
     * Tests that the check method returns a healthy state.
     *
     * @return void
     */
    public function testCheckReturnsHealthy(): void
    {
        $instance = $this->getInstance();
        $expected = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'The dispatch queue service is healthy.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }

    /**
     * Tests that the health check name and short name match the expected values on construction.
     *
     * @return void
     */
    public function testConstruction(): void
    {
        $instance = $this->getInstance();

        self::assertSame('Dispatch Queue', $instance->getName());
        self::assertSame('queue', $instance->getShortName());
    }

    /**
     * Gets an instance of the service for testing.
     *
     * @param \Illuminate\Contracts\Bus\Dispatcher|null $dispatcher
     *
     * @return \EoneoPay\Externals\Health\Checks\QueueHealthCheck
     */
    private function getInstance(
        ?Dispatcher $dispatcher = null
    ): QueueHealthCheck {
        return new QueueHealthCheck(
            $dispatcher ?? new DispatcherStub()
        );
    }
}
