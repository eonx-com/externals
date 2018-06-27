<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment\Interfaces;

interface LoaderInterface
{
    /**
     * Load the env file
     *
     * @return void
     */
    public function load(): void;
}
