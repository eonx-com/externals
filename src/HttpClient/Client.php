<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Throwable;

final class Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface
     */
    private $exceptionHandler;

    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface
     */
    private $streamParser;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface $exceptionHandler
     * @param \EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface $streamParser
     */
    public function __construct(
        GuzzleClientInterface $client,
        ExceptionHandlerInterface $exceptionHandler,
        StreamParserInterface $streamParser
    ) {
        $this->client = $client;
        $this->exceptionHandler = $exceptionHandler;
        $this->streamParser = $streamParser;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface
    {
        // Define exception in case request fails so the variable can be used outside of the
        // try block
        $exception = null;

        try {
            $response = $this->client->request($method, $uri, $options ?? []);
        } catch (GuzzleException $exception) {
            $response = $this->exceptionHandler->getResponseFrom($exception);
        }

        // If response is unsuccessful, throw exception
        $this->handleResponseFailure($response, $exception);

        return $this->buildResponse($response);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function sendRequest(RequestInterface $request, ?array $options = null): PsrResponseInterface
    {
        // Define exception in case request fails so the variable can be used outside of the
        // try block
        $exception = null;

        try {
            $response = $this->client->send($request, $options ?? []);
        } catch (GuzzleException $exception) {
            $response = $this->exceptionHandler->getResponseFrom($exception);
        }

        // If response is unsuccessful, throw exception
        $this->handleResponseFailure($response, $exception);

        return $response;
    }

    /**
     * Builds a response and parses body contents of the PsrResponse and puts
     * it into our Response object.
     *
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     *
     * @return \EoneoPay\Externals\HttpClient\Response
     */
    private function buildResponse(PsrResponseInterface $psrResponse): Response
    {
        $data = $this->streamParser->parse($psrResponse->getBody());

        return new Response($psrResponse, $data);
    }

    /**
     * Throws if the response isn't successful
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Throwable|null $exception
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    private function handleResponseFailure(PsrResponseInterface $response, ?Throwable $exception = null): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            // The response is successful

            return;
        }

        $errorResponse = $this->buildResponse($response);

        throw new InvalidApiResponseException($errorResponse, $exception);
    }
}
