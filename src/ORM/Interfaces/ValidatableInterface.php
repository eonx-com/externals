<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface ValidatableInterface extends EntityInterface
{
    /**
     * Get validation rules.
     *
     * @return mixed[]
     */
    public function getRules(): array;

    /**
     * Get validation failed exception class.
     *
     * @return string
     */
    public function getValidationFailedException(): string;
}
