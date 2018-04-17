<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Validation;

use Illuminate\Validation\Validator;

class CustomRules extends Validator
{
    /**
     * Ensure that an attribute is empty
     *
     * @param string $attribute The attribute being checked
     * @param mixed $value The attribute value
     *
     * @return bool
     */
    public function validateEmpty(string $attribute, $value): bool
    {
        return !$this->validateRequired($attribute, $value);
    }

    /**
     * Ensure that an attribute is empty if another attribute contains a value
     *
     * @param string $attribute The attribute being checked
     * @param mixed $value The attribute value
     * @param array $parameters Additional parameters
     *
     * @return bool
     */
    public function validateEmptyWith(string $attribute, $value, array $parameters): bool
    {
        $key = $parameters[0] ?? '';

        return $this->validateRequired($key, $this->getValue($key)) ?
            $this->validateEmpty($attribute, $value) :
            true;
    }
}
