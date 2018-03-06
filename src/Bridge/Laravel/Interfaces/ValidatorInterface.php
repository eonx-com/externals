<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Interfaces;

interface ValidatorInterface
{
    /**
     * Get messages from the last validation attempt
     *
     * @return array
     */
    public function getFailures(): array;

    /**
     * Validate the given data against the provided rules
     *
     * @param array $data Data to validate
     * @param array $rules Rules to validate against
     *
     * @return bool
     */
    public function validate(array $data, array $rules): bool;
}
