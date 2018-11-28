<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Validation;

use Closure;
use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;

class InstanceOfRule implements ValidationRuleInterface
{
    /**
     * Get rule name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'instance_of';
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
        return function (
            string $message,
            string $attribute,
            /** @noinspection PhpUnusedParameterInspection Parameters defined by interface */ string $rule,
            array $parameters
        ): string {
            return \str_replace([':attribute', ':values'], [$attribute, $parameters[0] ?? '{NO PARAMETER}'], $message);
        };
    }

    /**
     * Get the validation rule closure
     *
     * @return \Closure
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) $attribute on Closure are defined by Laravel validator
     */
    public function getRule(): Closure
    {
        return function (
            /** @noinspection PhpUnusedParameterInspection Parameters defined by interface */ string $attribute,
            $value,
            array $parameters
        ): bool {
            // If no value given just pass, to be able to validate optional attributes
            if ($value === null) {
                return true;
            }

            $class = $parameters[0] ?? null;

            // If no parameters given rule should fail
            if ($class === null) {
                return false;
            }

            return $value instanceof $class;
        };
    }
}
