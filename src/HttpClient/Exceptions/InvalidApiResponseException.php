<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Externals\HttpClient\Interfaces\InvalidApiResponseExceptionInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Exceptions\RuntimeException;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

final class InvalidApiResponseException extends RuntimeException implements
    RequestExceptionInterface,
    InvalidApiResponseExceptionInterface
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    private $response;

    /**
     * InvalidApiResponseException constructor.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface $response The response received from the api
     * @param \Throwable|null $previous The original exception thrown
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        ?Throwable $previous = null
    ) {
        /*
         * Because previous exception (Guzzle ClientException) truncates the response,
         * we add a full response message to our exception.
         */
        $message = sprintf(
            "Request resulted in a `%s %s` response: \n%s\n",
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getContent()
        );

        parent::__construct($message, null, 0, $previous);

        $this->request = $request;
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_RUNTIME;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return self::DEFAULT_ERROR_SUB_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get response.
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
