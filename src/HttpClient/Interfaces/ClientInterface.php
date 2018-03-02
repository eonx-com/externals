<?php
declare(strict_types=1);

namespace EoneoPay\External\HttpClient\Interfaces;

use EoneoPay\External\HttpClient\Exceptions\InvalidApiResponseException;

interface ClientInterface
{
    /**
     * Perform a request on a uri
     *
     * @param string $method The method to use for the request
     * @param string $uri The uri to send the request to
     * @param array|null $options The options to send with the request
     *
     * @return \EoneoPay\External\HttpClient\Interfaces\ResponseInterface A constructed api response
     *
     * @throws InvalidApiResponseException If response is not successful
     */
    public function request(string $method, string $uri, ?array $options = null): ResponseInterface;
}
