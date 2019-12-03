<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Interfaces;

interface ValidatorInterface
{
    /**
     * Validate the given data against the provided rules.
     *
     * @param mixed[] $data Data to validate
     * @param mixed[] $rules Rules to validate against
     *
     * @return mixed[]
     */
    public function validate(array $data, array $rules): array;
}
