<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\LoyaltyCorp\Search;

use LoyaltyCorp\Search\DataTransferObjects\ClusterHealth;
use LoyaltyCorp\Search\Interfaces\ClientInterface;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Max number of public methods is not limited for test stub
 */
class SearchClientStub implements ClientInterface
{
    /**
     * The cluster health.
     *
     * @var \LoyaltyCorp\Search\DataTransferObjects\ClusterHealth|null
     */
    private $health;

    /**
     * Constructs a new instance of the stub.
     *
     * @param \LoyaltyCorp\Search\DataTransferObjects\ClusterHealth|null $clusterHealth
     */
    public function __construct(
        ?ClusterHealth $clusterHealth = null
    ) {
        $this->health = $clusterHealth;
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $searchIds): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function bulkUpdate(array $updates): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function count(string $index): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function createAlias(string $indexName, string $aliasName): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex(string $name, ?array $mappings = null, ?array $settings = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAlias(array $aliases): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex(string $name): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(?string $name = null): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHealth(): ClusterHealth
    {
        return $this->health ?? new ClusterHealth([
                'cluster_name' => 'testcluster',
                'status' => 'green',
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
    }

    /**
     * {@inheritdoc}
     */
    public function getIndices(?string $name = null): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isAlias(string $name): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isIndex(string $name): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function moveAlias(array $aliases): void
    {
    }
}
