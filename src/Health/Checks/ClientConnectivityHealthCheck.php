<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthCheckInterface;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * This health check is used to check that a connection to a guzzle client can be made.
 * Details of response is not intended to be checked, but to make sure a connection attempt
 * does not throw exception.
 *
 * The endpoint to connect to must always be a GET.
 */
class ClientConnectivityHealthCheck implements HealthCheckInterface
{
    /**
     * The degraded state message.
     *
     * @const string
     */
    private const MESSAGE_DEGRADED = 'The connection to client is degraded.';

    /**
     * The healthy state message.
     *
     * @const string
     */
    private const MESSAGE_HEALTHY = 'The connection to client is healthy.';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Endpoint to connect to.
     *
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $name;

    /**
     * ClientConnectivityHealthCheck constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string $endpoint Endpoint to hit. This needs to be a GET method.
     * @param string $serviceName The name of service you are connecting to. Used when displaying service health.
     */
    public function __construct(ClientInterface $client, string $endpoint, string $serviceName)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->name = $serviceName;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): HealthState
    {
        $state = HealthInterface::STATE_DEGRADED;

        try {
            $this->client->request('GET', $this->endpoint, ['http_errors' => false, 'timeout' => 1]);

            $state = HealthInterface::STATE_HEALTHY;
        } catch (GuzzleException $exception) {
            // If exception occurs during the connection, service is not available.
        }

        return new HealthState(
            $state,
            $state === HealthInterface::STATE_HEALTHY
                ? $this->getHealthyMessage()
                : $this->getDegradedMessage()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return \sprintf('Connect to %s', $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName(): string
    {
        return $this->name;
    }

    /**
     * Get degraded message.
     *
     * @return string
     */
    protected function getDegradedMessage(): string
    {
        return self::MESSAGE_DEGRADED;
    }

    /**
     * Get healthy message.
     *
     * @return string
     */
    protected function getHealthyMessage(): string
    {
        return self::MESSAGE_HEALTHY;
    }
}
