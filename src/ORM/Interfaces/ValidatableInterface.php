<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface ValidatableInterface extends EntityInterface
{
    /**
     * Get all validatable properties for this entity.
     *
     * @return string[]
     */
    public function getValidatableProperties(): array;

    /**
     * Get validation rules.
     *
     * @return mixed[]
     */
    public function getRules(): array;

    /**
     * Get validation failed exception class.
     *
     * @phpstan-return class-string
     *
     * @return string
     */
    public function getValidationFailedException(): string;
}
