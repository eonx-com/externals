<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

interface ExceptionHandlerInterface
{
    /**
     * Retrieves the response from an exception
     *
     * @param \GuzzleHttp\Exception\GuzzleException $exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponseFrom(GuzzleException $exception): ResponseInterface;
}
