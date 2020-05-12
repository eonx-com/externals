<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Auditing;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use LoyaltyCorp\Auditing\Document;
use LoyaltyCorp\Auditing\Interfaces\ManagerInterface;
use LoyaltyCorp\Auditing\Interfaces\Services\UuidGeneratorInterface;

/**
 * @coversNothing
 */
class ManagerStub implements ManagerInterface
{
    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    private $client;

    /**
     * ManagerStub constructor.
     *
     * @param \Aws\DynamoDb\DynamoDbClient $client
     */
    public function __construct(DynamoDbClient $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getDbClient(): DynamoDbClient
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentObject(string $documentClass): Document
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getGenerator(): UuidGeneratorInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMarshaler(): Marshaler
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName(string $tableName): string
    {
    }
}
