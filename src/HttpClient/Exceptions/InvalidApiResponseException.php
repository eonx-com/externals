<?php
declare(strict_types=1);

namespace EoneoPay\External\HttpClient\Exceptions;

use EoneoPay\External\HttpClient\Interfaces\InvalidApiResponseExceptionInterface;
use EoneoPay\External\HttpClient\Interfaces\ResponseInterface;
use Exception;
use Throwable;

class InvalidApiResponseException extends Exception implements InvalidApiResponseExceptionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * InvalidApiResponseException constructor.
     *
     * @param string|null $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param \EoneoPay\External\HttpClient\Interfaces\ResponseInterface $response
     */
    public function __construct(
        Throwable $previous = null,
        ResponseInterface $response
    ) {
        parent::__construct('', 0, $previous);

        $this->response = $response;
    }

    /**
     * Get response.
     *
     * @return \EoneoPay\External\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
