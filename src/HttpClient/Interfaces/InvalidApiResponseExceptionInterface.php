<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

interface InvalidApiResponseExceptionInterface
{
    /**
     * Get response.
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
