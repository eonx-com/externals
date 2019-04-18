<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response as PsrResponse;

final class Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var null|\EoneoPay\Externals\Logger\Interfaces\LoggerInterface
     */
    private $logger;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\Client|null $client
     * @param \EoneoPay\Externals\Logger\Interfaces\LoggerInterface|null $logger
     */
    public function __construct(?Guzzle $client = null, ?LoggerInterface $logger = null)
    {
        $this->client = $client ?? new Guzzle();
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    public function proxy(RequestInterface $request, ?array $options = null): ResponseInterface
    {
        $this->logRequest($request->getMethod(), $request->getUri()->__toString(), $options);

        $exception = null;

        try {
            $guzzleResponse = $this->client->send($request, $options ?? []);

            $response = new Response($guzzleResponse);
        } catch (RequestException $exception) {
            $response = $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            // Covers any other guzzle exception
            $response = new Response(new PsrResponse($exception->getMessage(), 500));
        }

        $this->logResponse($response);

        // If response is unsuccessful, throw exception
        if ($response->isSuccessful() === false) {
            throw new InvalidApiResponseException($response, $exception);
        }

        return $response;
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface
    {
        $this->logRequest($method, $uri, $options);

        // Define exception in case request fails
        $exception = null;

        try {
            $guzzleResponse = $this->client->request($method, $uri, $options ?? []);

            $response = new Response($guzzleResponse);
        } catch (RequestException $exception) {
            $response = $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            // Covers any other guzzle exception
            $response = new Response(new PsrResponse($exception->getMessage(), 500));
        }

        $this->logResponse($response);

        // If response is unsuccessful, throw exception
        if ($response->isSuccessful() === false) {
            throw new InvalidApiResponseException($response, $exception);
        }

        return $response;
    }

    /**
     * Capture a request exception and convert it to an API response
     *
     * @param \GuzzleHttp\Exception\RequestException $exception The exception thrown
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    private function handleRequestException(RequestException $exception): ResponseInterface
    {
        $this->logException($exception);

        if ($exception->hasResponse() && $exception->getResponse() !== null) {
            return new Response($exception->getResponse());
        }

        $content = \json_encode(['exception' => $exception->getMessage()]) ?: '';

        return new Response(new PsrResponse($content, 400));
    }

    /**
     * Log the request exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    private function logException(Exception $exception): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->exception($exception);
    }

    /**
     * Log the outgoing request
     *
     * @param string $method The method to use for the request
     * @param string $uri The uri to send the request to
     * @param mixed[]|null $options The options to send with the request
     *
     * @return void
     */
    private function logRequest(string $method, string $uri, ?array $options = null): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->info('API request sent', \compact('method', 'uri', 'options'));
    }

    /**
     * Log the received response
     *
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface $response The received response
     *
     * @return void
     */
    private function logResponse(ResponseInterface $response): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->info('API response received', $response->toArray());
    }
}
