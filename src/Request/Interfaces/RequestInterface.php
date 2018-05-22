<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Request\Interfaces;

interface RequestInterface
{
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
     * Retrieve the entire request as an array
     *
     * @return array
     */
    public function toArray(): array;
}
