<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ClientInterface extends HttpClientInterface
{
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

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param mixed[]|null $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendRequest(RequestInterface $request, ?array $options = null): PsrResponseInterface;
}
