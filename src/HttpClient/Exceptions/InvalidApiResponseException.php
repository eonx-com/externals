<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Externals\HttpClient\Interfaces\InvalidApiResponseExceptionInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Exceptions\RuntimeException;
use Throwable;

class InvalidApiResponseException extends RuntimeException implements InvalidApiResponseExceptionInterface
{
    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    private $response;

    /**
     * InvalidApiResponseException constructor.
     *
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface $response The response received from the api
     * @param \Throwable|null $previous The original exception thrown
     */
    public function __construct(ResponseInterface $response, ?Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);

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
     * Get response.
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
