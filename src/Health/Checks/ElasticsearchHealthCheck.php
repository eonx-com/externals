<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use LoyaltyCorp\Search\Interfaces\ClientInterface;

class ElasticsearchHealthCheck implements HealthCheckInterface
{
    /**
     * The search client instance.
     *
     * @var \LoyaltyCorp\Search\Interfaces\ClientInterface
     */
    private $client;

    /**
     * Constructs a new instance of the Elasticsearch health check.
     *
     * @param \LoyaltyCorp\Search\Interfaces\ClientInterface $client
     */
    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        try {
            $this->client->getIndices();

            return new HealthState(
                HealthInterface::STATE_HEALTHY,
                'Communication with Elasticsearch was successful.'
            );
        } /** @noinspection BadExceptionsProcessingInspection */ catch (\Exception $exception) {
            // An exception indicates a failure to communicate with Elasticsearch.
        }

        return new HealthState(
            HealthInterface::STATE_DEGRADED,
            'Communication with Elasticsearch failed.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Elasticsearch';
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return 'search';
    }
}
