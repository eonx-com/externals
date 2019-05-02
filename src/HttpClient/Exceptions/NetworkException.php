<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * Indicates a network error has occurred.
 */
class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * Constructor
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Throwable $previous
     */
    public function __construct(
        RequestInterface $request,
        Throwable $previous
    ) {
        parent::__construct($previous->getMessage(), null, $previous->getCode(), $previous);

        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return static::DEFAULT_ERROR_CODE_RUNTIME;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
