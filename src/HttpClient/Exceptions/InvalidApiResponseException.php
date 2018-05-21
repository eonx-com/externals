<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Externals\HttpClient\Interfaces\InvalidApiResponseExceptionInterface;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use Exception;
use Throwable;

class InvalidApiResponseException extends Exception implements InvalidApiResponseExceptionInterface
{
    /**
     * @var \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    private $response;

    /**
     * InvalidApiResponseException constructor.
     *
     * @param \Throwable|null $previous
     * @param \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface $response
     */
    public function __construct(
        ?Throwable $previous = null,
        ResponseInterface $response
    ) {
        parent::__construct('', 0, $previous);

        $this->response = $response;
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
