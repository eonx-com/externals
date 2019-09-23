<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use function GuzzleHttp\Psr7\str;

final class LoggingClient implements ClientInterface
{
    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ClientInterface
     */
    private $client;

    /**
     * @var \EoneoPay\Externals\Logger\Interfaces\LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ClientInterface $client
     * @param \EoneoPay\Externals\Logger\Interfaces\LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception Rethrows any exception caught from Guzzle
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface
    {
        $request = new Request(
            $method,
            $uri,
            $options['headers'] ?? [],
            $options['body'] ?? null,
            $options['version'] ?? '1.1'
        );

        $this->logRequest($request, $options);

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (Exception $exception) {
            $this->logException($exception, $request);

            // To avoid type hinting @throws \Exception, we suppress this throw - it is only
            // intended to rethrow things that other ClientInterface implementations may throw.

            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }

        $this->logResponse($response, $request);

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception Rethrows any exception caught from Guzzle
     */
    public function sendRequest(RequestInterface $request, ?array $options = null): PsrResponseInterface
    {
        $this->logRequest($request, $options);

        try {
            $response = $this->client->sendRequest($request, $options);
        } catch (Exception $exception) {
            $this->logException($exception, $request);

            // To avoid type hinting @throws \Exception, we suppress this throw - it is only
            // intended to rethrow things that other ClientInterface implementations may throw.

            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }

        $this->logResponse($response, $request);

        return $response;
    }

    /**
     * Log the request exception.
     *
     * @param \Exception $exception
     * @param \Psr\Http\Message\RequestInterface|null $request
     *
     * @return void
     */
    private function logException(Exception $exception, ?RequestInterface $request = null): void
    {
        while ($exception->getPrevious() !== null) {
            // Unwind wrapped exceptions to their root
            $exception = $exception->getPrevious();
        }

        $response = null;

        if (($exception instanceof RequestException) === true) {
            /**
             * @var \GuzzleHttp\Exception\ServerException $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            $response = $exception->getResponse();
        }

        if ($response instanceof PsrResponseInterface === true) {
            $this->logResponse($response);
        }

        $this->logger->exception($exception, null, [
            'request' => $request !== null ? str($request) : null,
            'uri' => $request !== null ? (string)$request->getUri() : null,
        ]);
    }

    /**
     * Log the outgoing request.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param mixed[]|null $options The options to send with the request
     *
     * @return void
     */
    private function logRequest(RequestInterface $request, ?array $options = null): void
    {
        $this->logger->info('HTTP Request Sent', [
            'options' => $options ?? [],
            'request' => str($request),
            'uri' => (string)$request->getUri(),
        ]);
    }

    /**
     * Log the received response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The received response
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    private function logResponse(PsrResponseInterface $response, ?RequestInterface $request = null): void
    {
        $this->logger->info('HTTP Response Received', [
            'request' => $request !== null ? str($request) : null,
            'response' => str($response),
            'statusCode' => $response->getStatusCode(),
            'uri' => $request !== null ? (string)$request->getUri() : null,
        ]);

        if ($response->getBody()->isSeekable()) {
            // We've moved the pointer of the stream to the end, lets move it back.
            $response->getBody()->rewind();
        }
    }
}
