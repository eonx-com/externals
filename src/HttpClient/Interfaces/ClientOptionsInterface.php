<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

interface ClientOptionsInterface
{
    /**
     * Gets the numbers of seconds the HTTP client will wait to connect before timing out.
     *
     * @return float
     */
    public function getConnectTimeout(): float;

    /**
     * Gets the numbers of seconds the HTTP client will wait for the response body to finish
     * streaming before timing out.
     *
     * @return float
     */
    public function getReadTimeout(): float;

    /**
     * Gets the numbers of seconds the HTTP client will wait for a response before timing out.
     *
     * @return float
     */
    public function getRequestTimeout(): float;

    /**
     * Sets the numbers of seconds the HTTP client will wait for to connect before timing out.
     *
     * @param float $timeout
     *
     * @return void
     */
    public function setConnectTimeout(float $timeout): void;

    /**
     * Sets the numbers of seconds the HTTP client will wait for the response body to finish
     * streaming before timing out.
     *
     * @param float $timeout
     *
     * @return void
     */
    public function setReadTimeout(float $timeout): void;

    /**
     * Sets the numbers of seconds the HTTP client will wait for a response before timing out.
     *
     * @param float $timeout
     *
     * @return void
     */
    public function setRequestTimeout(float $timeout): void;

    /**
     * Returns the client options in an array that is compatible for use with Guzzle.
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
