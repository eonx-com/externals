<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment\Interfaces;

interface LoaderInterface
{
    /**
     * Load the env file and preserve existing values.
     *
     * @return void
     */
    public function load(): void;

    /**
     * Load the env file and overwrite existing values.
     *
     * @return void
     */
    public function overload(): void;
}
