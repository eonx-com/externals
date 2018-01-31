<?php
declare(strict_types=1);

namespace EoneoPay\External\HttpClient;

use EoneoPay\External\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\External\HttpClient\Interfaces\ClientInterface;
use EoneoPay\External\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\External\Logger\Interfaces\LoggerInterface;
use EoneoPay\Utils\Exceptions\InvalidXmlException;
use EoneoPay\Utils\XmlConverter;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\Client $client
     * @param \EoneoPay\External\Logger\Interfaces\LoggerInterface|null $logger
     */
    public function __construct(Guzzle $client, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Perform a request on a uri
     *
     * @param string $method The method to use for the request
     * @param string $uri The uri to send the request to
     * @param array|null $options The options to send with the request
     *
     * @return \EoneoPay\External\HttpClient\Interfaces\ResponseInterface A constructed api response
     *
     * @throws \EoneoPay\External\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface
    {
        $this->logRequest($method, $uri, $options);

        try {
            $request = $this->client->request($method, $uri, $options ?? []);
            $content = $this->getBodyContents($request->getBody());

            $response = new Response(
                $this->processResponseContent($content),
                $request->getStatusCode(),
                $request->getHeaders(),
                $content
            );
        } catch (RequestException $exception) {
            $response = $this->handleRequestException($exception);
        }

        $this->logResponse($response);

        // If response is unsuccessful, throw exception
        if ($response->isSuccessful() === false) {
            throw new InvalidApiResponseException($exception ?? null, $response);
        }

        return $response;
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
        } catch (RuntimeException $exception) {
            $this->logException($exception);

            return '';
        }
    }

    /**
     * Capture a request exception and convert it to an API response
     *
     * @param \GuzzleHttp\Exception\RequestException $exception The exception thrown
     *
     * @return \EoneoPay\External\HttpClient\Interfaces\ResponseInterface
     */
    private function handleRequestException(RequestException $exception): ResponseInterface
    {
        $this->logException($exception);

        if ($exception->hasResponse() && $exception->getResponse() !== null) {
            $content = $this->getBodyContents($exception->getResponse()->getBody());

            return new Response(
                $this->processResponseContent($content),
                $exception->getResponse()->getStatusCode(),
                $exception->getResponse()->getHeaders(),
                $content
            );
        }

        $content = \json_encode(['exception' => $exception->getMessage()]);

        return new Response($this->processResponseContent($content), 400, null, $content);
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
        \json_decode($string);

        return \json_last_error() === JSON_ERROR_NONE;
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
        if (null === $this->logger) {
            return;
        }

        $this->logger->exception($exception);
    }

    /**
     * Log the outgoing request
     *
     * @param string $method The method to use for the request
     * @param string $uri The uri to send the request to
     * @param array|null $options The options to send with the request
     *
     * @return void
     */
    private function logRequest(string $method, string $uri, ?array $options = null): void
    {
        if (null === $this->logger) {
            return;
        }

        $this->logger->info('API request sent', \compact('method', 'uri', 'options'));
    }

    /**
     * Log the received response
     *
     * @param \EoneoPay\External\HttpClient\Interfaces\ResponseInterface The received response
     *
     * @return void
     */
    private function logResponse(ResponseInterface $response): void
    {
        if (null === $this->logger) {
            return;
        }

        $this->logger->info('API response received', $response->toArray());
    }

    /**
     * Process response body into an array.
     *
     * @param string $content
     *
     * @return array|null
     */
    private function processResponseContent(string $content): ?array
    {
        // If contents is json, decode it
        if ($this->isJson($content) === true) {
            return \json_decode($content, true);
        }

        // If content is xml, decode it
        if ($this->isXml($content) === true) {
            try {
                return (new XmlConverter())->xmlToArray($content);
                // @codeCoverageIgnoreStart
            } catch (InvalidXmlException $exception) {
                $this->logException($exception);
            }
            // @codeCoverageIgnoreEnd
        }

        // Return result as array
        return ['content' => $content];
    }
}
