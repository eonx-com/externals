<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Request\Interfaces;

interface RequestInterface
{
    /**
     * Get client ip address
     *
     * @return string|null
     */
    public function getClientIp(): ?string;

    /**
     * Get a header by name
     *
     * @param string $key The key to find
     * @param mixed $default The default to return if key isn't found
     *
     * @return mixed
     */
    public function getHeader(string $key, $default = null);

    /**
     * Retrieve the server host
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Get user from request
     *
     * @return string|null
     */
    public function getUser(): ?string;

    /**
     * Determine if the request contains a given input item key
     *
     * @param string $key The key to find
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Retrieve an input item from the request
     *
     * @param string|null $key The key to retrieve from the input
     * @param mixed $default The default value to use if key isn't set
     *
     * @return mixed
     */
    public function input(?string $key = null, $default = null);

    /**
     * Merge a new data set into an existing request
     *
     * @param mixed[] $data The data to merge into the request
     *
     * @return static
     */
    public function merge(array $data);

    /**
     * Replace request with a new set of data
     *
     * @param mixed[] $data The data to replace in the request
     *
     * @return static
     */
    public function replace(array $data);

    /**
     * Set a header on the request
     *
     * @param string $key The key to set
     * @param mixed $value The value to set against the header
     *
     * @return static
     */
    public function setHeader(string $key, $value);

    /**
     * Retrieve the entire request as an array
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
