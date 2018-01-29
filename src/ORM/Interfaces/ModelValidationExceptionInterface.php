<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Interfaces;

use Illuminate\Support\MessageBag;

interface ModelValidationExceptionInterface
{
    /**
     * Get message bag.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag(): MessageBag;
}
