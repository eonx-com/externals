<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Utils\Exceptions\InvalidXmlException;
use EoneoPay\Utils\XmlConverter;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\str;

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
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function proxy(RequestInterface $request, ?array $options = null): PsrResponseInterface
    {
        $this->logRequest($request->getMethod(), $request->getUri()->__toString(), $options);

        // Define exception in case request fails
        $exception = null;

        try {
            $response = $this->client->send($request, $options ?? []);
        } catch (RequestException $exception) {
            $response = $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            // Covers any other guzzle exception
            $response = new PsrResponse(500);
        }

        $this->logResponse($response);

        // If response is unsuccessful, throw exception
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 300 || $statusCode < 200) {
            throw new InvalidApiResponseException(new Response($response), $exception);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface
    {
        $this->logRequest($method, $uri, $options);

        // Define exception in case request fails
        $exception = null;
        $content = null;

        try {
            $response = $this->client->request($method, $uri, $options ?? []);
        } catch (RequestException $exception) {
            $response = $this->handleRequestException($exception);
        } catch (GuzzleException $exception) {
            // Covers any other guzzle exception
            $response = new PsrResponse(500);
            $content = ['contents' => $exception->getMessage()];
        }

        $this->logResponse($response);

        if ($content === null) {
            $content = $this->processResponseContent($this->getBodyContents($response->getBody()));
        }

        // If response is unsuccessful, throw exception
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 300 || $statusCode < 200) {
            throw new InvalidApiResponseException(new Response($response, $content), $exception);
        }

        return new Response($response, $content);
    }

    /**
     * Get response body contents.
     *
     * @param \Psr\Http\Message\StreamInterface $body
     *
     * @return string
     */
    private function getBodyContents(StreamInterface $body): string
    {
        try {
            return $body->getContents();
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            // This exception is unlikely as the stream is retrieved directly from Guzzle
            $this->logException($exception);

            return '';
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Capture a request exception and convert it to an API response
     *
     * @param \GuzzleHttp\Exception\RequestException $exception The exception thrown
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleRequestException(RequestException $exception): PsrResponseInterface
    {
        $this->logException($exception);

        if ($exception->getResponse() !== null) {
            return $exception->getResponse();
        }

        $content = \json_encode(['exception' => $exception->getMessage()]) ?: '';

        return new PsrResponse(400, [], $content);
    }

    /**
     * Determine if a string is json
     *
     * @param string $string The string to check
     *
     * @return bool
     */
    private function isJson(string $string): bool
    {
        \json_decode($string, false);

        return \json_last_error() === \JSON_ERROR_NONE;
    }

    /**
     * Determine if a string is xml
     *
     * @param string $string The string to check
     *
     * @return bool
     */
    private function isXml(string $string): bool
    {
        \libxml_use_internal_errors(true);

        return \simplexml_load_string($string) !== false;
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
     * @param \Psr\Http\Message\ResponseInterface $response The received response
     *
     * @return void
     */
    private function logResponse(PsrResponseInterface $response): void
    {
        if ($this->logger === null) {
            return;
        }

        $this->logger->info('API response received', ['response' => str($response)]);
    }

    /**
     * Process response body into an array.
     *
     * @param string $content
     *
     * @return mixed[]|null
     */
    private function processResponseContent(string $content): ?array
    {
        // If content is xml, decode it
        if ($this->isXml($content) === true) {
            try {
                return (new XmlConverter())->xmlToArray($content);
                // @codeCoverageIgnoreStart
            } catch (InvalidXmlException $exception) {
                // This exception is unlikely as the `isXML()` method would return false
                // if the content contains invalid/unparseable XML
                $this->logException($exception);
                // @codeCoverageIgnoreEnd
            }
        }

        // If contents is json, decode it otherwise encase in array
        return $this->isJson($content) === true ?
            \json_decode($content, true) :
            ['content' => $content];
    }
}
