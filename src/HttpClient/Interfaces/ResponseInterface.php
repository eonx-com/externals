<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use EoneoPay\Utils\Interfaces\CollectionInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends CollectionInterface, PsrResponseInterface
{
    /**
     * Get response content
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Determine if the response is successful or not
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
