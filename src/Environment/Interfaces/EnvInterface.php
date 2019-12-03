<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment\Interfaces;

interface EnvInterface
{
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key The key to get
     * @param mixed $default The value to return if the key isn't set
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Remove an environment variable.
     *
     * @param string $key The key to remove
     *
     * @return bool
     */
    public function remove(string $key): bool;

    /**
     * Set an environment variable.
     *
     * @param string $key The key to set
     * @param mixed $value The value to assign to the key
     *
     * @return bool
     */
    public function set(string $key, $value): bool;
}
