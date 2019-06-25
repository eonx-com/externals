<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Interfaces;

interface ValidatorInterface
{
    /**
     * Add a custom rule to the validator
     *
     * @param string $className The class this rule uses
     *
     * @return void
     */
    public function addCustomRule(string $className): void;

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
     * @param mixed[] $data
     * @param mixed[] $rules
     *
     * @return mixed[]
     */
    public function validatedData(array $data, array $rules): array;
}
