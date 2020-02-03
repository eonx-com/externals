<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use Aws\DynamoDb\DynamoDbClient;
use Aws\MockHandler;
use Aws\Result;
use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Checks\DynamoDbHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Eonx\TestUtils\TestCases\UnitTestCase;
use Tests\EoneoPay\Externals\Stubs\Vendor\Auditing\ManagerStub;

/**
 * @covers \EoneoPay\Externals\Health\Checks\DynamoDbHealthCheck
 */
class DynamoDbHealthCheckTest extends UnitTestCase
{
    /**
     * Test healthy check status.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testHealthyStatusCheck(): void
    {
        $mock = new MockHandler();
        $mock->append(new Result([]));

        $client = new DynamoDbClient([
            'region' => 'ap-southeast-2',
            'version' => 'latest',
            'handler' => $mock,
            'credentials' => false
        ]);

        $manager = new ManagerStub($client);
        $instance = new DynamoDbHealthCheck($manager);

        $expected = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'The dynamo database service is healthy.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
        self::assertSame('DynamoDb', $instance->getShortName());
        self::assertSame('Connect to DynamoDb', $instance->getName());
    }

    /**
     * Test degraded check status.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testDegradedStatusCheck(): void
    {
        // Dynamo db client missing handler.
        $client = new DynamoDbClient([
            'region' => 'ap-southeast-2',
            'version' => 'latest'
        ]);

        $manager = new ManagerStub($client);
        $instance = new DynamoDbHealthCheck($manager);

        $expected = new HealthState(
            HealthInterface::STATE_DEGRADED,
            'The dynamo database service is degraded.'
        );

        $result = $instance->check();

        self::assertEquals($expected, $result);
    }
}
