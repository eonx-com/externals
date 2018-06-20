<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use EoneoPay\Utils\Interfaces\BaseExceptionInterface;

interface InvalidApiResponseExceptionInterface extends BaseExceptionInterface
{
    /**
     * Get response.
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
