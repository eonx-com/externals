<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface
{
    /**
     * Get response content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Determine if the response is successful or not.
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
