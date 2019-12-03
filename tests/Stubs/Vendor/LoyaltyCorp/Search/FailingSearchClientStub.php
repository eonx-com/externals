<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\LoyaltyCorp\Search;

use LoyaltyCorp\Search\Exceptions\SearchCheckerException;
use LoyaltyCorp\Search\Exceptions\SearchDeleteException;
use LoyaltyCorp\Search\Exceptions\SearchUpdateException;
use LoyaltyCorp\Search\Interfaces\ClientInterface;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Max number of public methods is not limited for test stub
 */
class FailingSearchClientStub implements ClientInterface
{
    /**
     * The exception to throw.
     *
     * @var \Exception|null
     */
    private $exception;

    /**
     * SearchClientStub constructor.
     *
     * @param \Exception $exception The exception to throw.
     */
    public function __construct(?\Exception $exception = null)
    {
        $this->exception = $exception;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchDeleteException
     * @throws \Exception
     */
    public function bulkDelete(array $searchIds): void
    {
        throw $this->exception
            ?? new SearchDeleteException('An error occurred while performing bulk delete on backend', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchUpdateException
     * @throws \Exception
     */
    public function bulkUpdate(array $updates): void
    {
        throw $this->exception
            ?? new SearchUpdateException('An error occurred while performing bulk update on backend', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchCheckerException
     * @throws \Exception
     */
    public function count(string $index): int
    {
        throw $this->exception
            ?? new SearchCheckerException('Unable to count number of documents within index', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchUpdateException
     * @throws \Exception
     */
    public function createAlias(string $indexName, string $aliasName): void
    {
        throw $this->exception
            ?? new SearchUpdateException('Unable to add alias', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchUpdateException
     * @throws \Exception
     */
    public function createIndex(string $name, ?array $mappings = null, ?array $settings = null): void
    {
        throw $this->exception
            ?? new SearchUpdateException('Unable to create new index', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchDeleteException
     * @throws \Exception
     */
    public function deleteAlias(array $aliases): void
    {
        throw $this->exception ??
            new SearchDeleteException('Unable to delete alias', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchDeleteException
     * @throws \Exception
     */
    public function deleteIndex(string $name): void
    {
        throw $this->exception
            ?? new SearchDeleteException('Unable to delete index', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchCheckerException
     * @throws \Exception
     */
    public function getAliases(?string $name = null): array
    {
        throw $this->exception
            ?? new SearchCheckerException('An error occurred obtaining a list of aliases', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchCheckerException
     * @throws \Exception
     */
    public function getIndices(?string $name = null): array
    {
        throw $this->exception ??
            new SearchCheckerException('An error occurred obtaining a list of indices', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchCheckerException
     * @throws \Exception
     */
    public function isAlias(string $name): bool
    {
        throw $this->exception
            ?? new SearchCheckerException('An error occurred checking if alias exists', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchCheckerException
     * @throws \Exception
     */
    public function isIndex(string $name): bool
    {
        throw $this->exception
            ?? new SearchCheckerException('An error occurred checking if index exists', 0);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Search\Exceptions\SearchUpdateException
     * @throws \Exception
     */
    public function moveAlias(array $aliases): void
    {
        throw $this->exception
            ?? new SearchUpdateException('Unable to atomically swap alias', 0);
    }
}
