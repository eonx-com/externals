<?php
declare(strict_types=1);

namespace EoneoPay\External\HttpClient\Interfaces;

interface InvalidApiResponseExceptionInterface
{
    /**
     * Get response.
     *
     * @return \EoneoPay\External\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
