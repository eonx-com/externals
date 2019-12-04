<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\Health\Checks\ElasticsearchHealthCheck;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use LoyaltyCorp\Search\DataTransferObjects\ClusterHealth;
use LoyaltyCorp\Search\Interfaces\ClientInterface as SearchClientInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\LoyaltyCorp\Search\FailingSearchClientStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\LoyaltyCorp\Search\SearchClientStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Health\Checks\ElasticsearchHealthCheck
 */
class ElasticsearchHealthCheckTest extends TestCase
{
    /**
     * Tests that the check returns a degraded result when the search client throws an exception.
     *
     * @return void
     */
    public function testCheckReturnsDegardedStateWithRedStateResponse(): void
    {
        $health = new ClusterHealth([
            'cluster_name' => 'testcluster',
            'status' => 'red',
            'timed_out' => false,
            'number_of_nodes' => 1,
            'number_of_data_nodes' => 2,
            'active_primary_shards' => 3,
            'active_shards' => 4,
            'relocating_shards' => 5,
            'initializing_shards' => 6,
            'unassigned_shards' => 7,
            'delayed_unassigned_shards' => 8,
            'number_of_pending_tasks' => 9,
            'number_of_in_flight_fetch' => 10,
            'task_max_waiting_in_queue_millis' => 11,
            'active_shards_percent_as_number' => 50.0,
        ]);
        $client = new SearchClientStub($health);
        $health = $this->getInstance($client);

        $result = $health->check();

        self::assertSame(HealthInterface::STATE_DEGRADED, $result->getState());
        self::assertSame('Communication with Elasticsearch failed.', $result->getMessage());
    }

    /**
     * Tests that the check returns a degraded result when the communication with Elasticsearch throws an exception.
     *
     * @return void
     */
    public function testCheckReturnsDegradedStateDueToExceptionThrown(): void
    {
        $client = new FailingSearchClientStub();
        $health = $this->getInstance($client);

        $result = $health->check();

        self::assertSame(HealthInterface::STATE_DEGRADED, $result->getState());
        self::assertSame('Communication with Elasticsearch failed.', $result->getMessage());
    }

    /**
     * Tests that the check returns a successful result.
     *
     * @return void
     */
    public function testCheckReturnsSuccessful(): void
    {
        $check = $this->getInstance();

        $result = $check->check();

        self::assertSame(HealthInterface::STATE_HEALTHY, $result->getState());
        self::assertSame('Communication with Elasticsearch was successful.', $result->getMessage());
    }

    /**
     * Tests that the values returned by the getters are the same as those set on construction.
     *
     * @return void
     */
    public function testConstruction(): void
    {
        $instance = $this->getInstance();

        self::assertSame('Elasticsearch', $instance->getName());
        self::assertSame('search', $instance->getShortName());
    }

    /**
     * Gets an instance of the health check for testing.
     *
     * @param \LoyaltyCorp\Search\Interfaces\ClientInterface|null $client
     *
     * @return \EoneoPay\Externals\Health\Checks\ElasticsearchHealthCheck
     */
    private function getInstance(
        ?SearchClientInterface $client = null
    ): HealthCheckInterface {
        return new ElasticsearchHealthCheck(
            $client ?? new SearchClientStub()
        );
    }
}
