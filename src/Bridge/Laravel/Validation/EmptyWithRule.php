<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Validation;

use Closure;
use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Utils\Arr;
use Illuminate\Validation\Validator;

class EmptyWithRule implements ValidationRuleInterface
{
    /**
     * Get rule name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'empty_with';
    }

    /**
     * Get message replacements
     *
     * @return \Closure
     *
     * * @SuppressWarnings(PHPMD.UnusedLocalVariable) $rule on Closure are defined by Laravel validator
     */
    public function getReplacements(): Closure
    {
        // Create replacement for message to include parameters
        return function (string $message, string $attribute, string $rule, array $parameters) {
            return \str_replace([':attribute', ':values'], [$attribute, implode(' / ', $parameters)], $message);
        };
    }

    /**
     * Get the validation rule closure
     *
     * @return \Closure
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) $attribute and $value on Closure are defined by Laravel validator
     */
    public function getRule(): Closure
    {
        // Ensure that the field is empty if one of the specified parameters isn't
        return function (string $attribute, $value, array $parameters, Validator $validator) {
            // Since getValue() is protected we need to use getData() to get parameter value using Arr in the
            // same way Laravel does for the getValue() method
            $arr = new Arr();

            // Check each parameter isn't set
            foreach ($parameters as $parameter) {
                // If parameter contains a value fail
                if (true === $validator->validateRequired($parameter, $arr->get($validator->getData(), $parameter))) {
                    return false;
                }
            }

            // If there hasn't been a return yet it's all good
            return true;
        };
    }
}
