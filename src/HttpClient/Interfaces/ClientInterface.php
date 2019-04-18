<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ClientInterface
{
    /**
     * Perform a request as supplied
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param mixed[]|null $options The options to send with the request
     *
     * @return \Psr\Http\Message\ResponseInterface A constructed api response
     */
    public function proxy(RequestInterface $request, ?array $options = null): PsrResponseInterface;

    /**
     * Perform a request on a uri
     *
     * @param string $method The method to use for the request
     * @param string $uri The uri to send the request to
     * @param mixed[]|null $options The options to send with the request
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface A constructed api response
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface;
}
