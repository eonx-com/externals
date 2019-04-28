<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;

final class ClientFactory
{
    /**
     * Creates and configures a HttpClient
     *
     * @param string|null $baseUri
     * @param mixed[]|null $options
     * @param \EoneoPay\Externals\Logger\Interfaces\LoggerInterface|null $logger
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ClientInterface
     */
    public function create(
        ?string $baseUri = null,
        ?array $options = null,
        ?LoggerInterface $logger = null
    ): ClientInterface {
        $logger = $logger ?? new Logger();

        $guzzle = new \GuzzleHttp\Client(\array_merge(
            $options ?? [],
            ['base_uri' => $baseUri]
        ));

        $innerClient = new Client(
            $guzzle,
            new ExceptionHandler(),
            new StreamParser()
        );

        return new LoggingClient($innerClient, $logger);
    }
}
