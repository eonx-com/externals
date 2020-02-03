<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\ClientConnectivityHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Eonx\TestUtils\TestCases\UnitTestCase;
use Tests\EoneoPay\Externals\Stubs\Vendor\Guzzle\ClientStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Guzzle\FailingClientStub;

/**
 * @covers \EoneoPay\Externals\Health\Checks\ClientConnectivityHealthCheck
 */
class ClientConnectivityHealthCheckTest extends UnitTestCase
{
    /**
     * Test client connection is successful.
     *
     * @return void
     */
    public function testClientConnection(): void
    {
        $client = new ClientStub();
        $instance = new ClientConnectivityHealthCheck(
            $client,
            '/health',
            'EoneoPay'
        );
        $expected = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'The connection to client is healthy.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
        self::assertSame('Connect to EoneoPay', $instance->getName());
        self::assertSame('EoneoPay', $instance->getShortName());
    }

    /**
     * Test check returns degraded status when connection fails.
     *
     * @return void
     */
    public function testClientConnectionFails(): void
    {
        $client = new FailingClientStub();
        $instance = new ClientConnectivityHealthCheck(
            $client,
            '/health',
            'EoneoPay'
        );
        $expected = new HealthState(
            HealthInterface::STATE_DEGRADED,
            'The connection to client is degraded.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }
}
