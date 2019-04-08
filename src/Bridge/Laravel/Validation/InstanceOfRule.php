<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Validation;

use Closure;
use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;

final class InstanceOfRule implements ValidationRuleInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'instance_of';
    }

    /**
     * @inheritdoc
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
            return \str_replace([':attribute', ':values'], [$attribute, $parameters[0] ?? '{NO PARAMETER}'], $message);
        };
    }

    /**
     * @inheritdoc
     */
    public function getRule(): Closure
    {
        return static function (
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
