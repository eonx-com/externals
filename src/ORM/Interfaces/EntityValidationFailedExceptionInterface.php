<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Interfaces;

interface EntityValidationFailedExceptionInterface
{
    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array;
}
