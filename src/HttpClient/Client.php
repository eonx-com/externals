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
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

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
        $request = new Request(
            $method,
            $uri,
            $options['headers'] ?? [],
            $options['body'] ?? null,
            $options['version'] ?? '1.1'
        );

        $response = $this->sendRequest($request, $options);

        if (($response instanceof ResponseInterface) === true) {
            /**
             * @var \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface $response
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            return $response;
        }

        // @codeCoverageIgnoreStart
        // It isnt possible to reach this code - $this->sendRequest can only be typed to return a
        // PSR ResponseInterface, but we're always returning our ResponseInterface.
        return new Response($response);
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    public function sendRequest(RequestInterface $request, ?array $options = null): PsrResponseInterface
    {
        // Defined so we can pass an exception to buildResponse if one occurred.
        $exception = null;

        try {
            $response = $this->client->send($request, $options ?? []);
        } catch (GuzzleException $exception) {
            $response = $this->exceptionHandler->handle($request, $exception);
        }

        return $this->buildResponse($request, $response, $exception);
    }

    /**
     * Builds a response and parses body contents of the PsrResponse and puts
     * it into our Response object.
     *
     * If an exception occurred while making the request, we will throw the
     * Response as part of an InvalidApiResponseException.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     * @param \GuzzleHttp\Exception\GuzzleException|null $exception
     *
     * @return \EoneoPay\Externals\HttpClient\Response
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException
     */
    private function buildResponse(
        RequestInterface $request,
        PsrResponseInterface $psrResponse,
        ?GuzzleException $exception
    ): Response {
        $data = $this->streamParser->parse($psrResponse->getBody());
        $response = new Response($psrResponse, $data);

        if ($exception !== null) {
            /**
             * @var \Throwable $exception
             *
             * Concrete guzzle exceptions implement \Throwable, their root interface
             * doesnt.
             */
            throw new InvalidApiResponseException($request, $response, $exception);
        }

        return $response;
    }
}
