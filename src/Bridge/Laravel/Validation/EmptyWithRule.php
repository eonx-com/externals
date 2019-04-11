<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Validation;

use Closure;
use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Utils\Arr;
use Illuminate\Validation\Validator;

final class EmptyWithRule implements ValidationRuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'empty_with';
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) Closure signature is defined by Laravel validator
     */
    public function getReplacements(): Closure
    {
        // Create replacement for message to include parameters
        return static function (
            string $message,
            string $attribute,
            /** @noinspection PhpUnusedParameterInspection Parameters defined by interface */ string $rule,
            array $parameters
        ): string {
            return \str_replace([':attribute', ':values'], [$attribute, \implode(' / ', $parameters)], $message);
        };
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) Closure signature is defined by Laravel validator
     */
    public function getRule(): Closure
    {
        // Ensure that the field is empty if one of the specified parameters isn't
        return static function (
            /** @noinspection PhpUnusedParameterInspection Parameters defined by interface */ string $attribute,
            /** @noinspection PhpUnusedParameterInspection Parameters defined by interface */ $value,
            array $parameters,
            Validator $validator
        ): bool {
            // Since getValue() is protected we need to use getData() to get parameter value using Arr in the
            // same way Laravel does for the getValue() method
            $arr = new Arr();

            // Check each parameter isn't set
            foreach ($parameters as $parameter) {
                // If parameter contains a value fail
                if ($validator->validateRequired($parameter, $arr->get($validator->getData(), $parameter)) === true) {
                    return false;
                }
            }

            // If there hasn't been a return yet it's all good
            return true;
        };
    }
}
