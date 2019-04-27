<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use EoneoPay\Utils\Interfaces\CollectionInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends CollectionInterface
{
    /**
     * Get response content
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Get response header.
     *
     * @param string $key
     *
     * @return null|string
     */
    public function getHeader(string $key): ?string;

    /**
     * Get response headers
     *
     * @return mixed[]
     */
    public function getHeaders(): array;

    /**
     * Returns the underlying Psr Response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPsrResponse(): PsrResponseInterface;

    /**
     * Get response status code
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Determine if the response is successful or not
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
