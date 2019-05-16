<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Interfaces;

interface ValidatorInterface
{
    /**
     * Get messages from the last validation attempt
     *
     * @return mixed[]
     */
    public function getFailures(): array;

    /**
     * Validate the given data against the provided rules
     *
     * @param mixed[] $data Data to validate
     * @param mixed[] $rules Rules to validate against
     *
     * @return bool
     */
    public function validate(array $data, array $rules): bool;

    /**
     * Return validated values
     *
     * @return mixed[]
     */
    public function validated(): array;
}
