<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces\Listeners;

interface GenerateUniqueValueInterface
{
    /**
     * Returns if generators are enabled or not
     *
     * @return bool
     */
    public function areGeneratorsEnabled(): bool;

    /**
     * Disable generators
     *
     * @return static
     */
    public function disableGenerators();

    /**
     * Enable generators
     *
     * @return static
     */
    public function enableGenerators();

    /**
     * Property name that will be populated with a random alphanumeric value and a check digit.
     *
     * @return string
     */
    public function getGeneratedProperty(): string;

    /**
     * Length of value to be generated (after check digit)
     *
     * @return int
     */
    public function getGeneratedPropertyLength(): int;

    /**
     * Boolean controlling whether or not value should include a check digit.
     *
     * @return bool
     */
    public function hasGeneratedPropertyCheckDigit(): bool;
}
