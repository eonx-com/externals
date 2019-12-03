<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\LoyaltyCorp\Search;

use LoyaltyCorp\Search\Interfaces\ClientInterface;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Max number of public methods is not limited for test stub
 */
class SearchClientStub implements ClientInterface
{
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
