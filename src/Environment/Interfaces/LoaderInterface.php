<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment\Interfaces;

interface LoaderInterface
{
    /**
     * Load the env file and preserve existing values
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function load(): void;

    /**
     * Load the env file and overwrite existing values
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function overload(): void;
}
