<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Externals\Bridge\Laravel\Validation\EmptyWithRule;
use EoneoPay\Externals\Bridge\Laravel\Validation\InstanceOfRule;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\PresenceVerifierInterface;

final class Validator implements ValidatorInterface
{
    /**
     * Validation factory instance
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    private $factory;

    /**
     * Database presence verifier
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface|null
     */
    private $presence;

    /**
     * Validation instance
     *
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Create new validation instance
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory Validation factory interface instance
     * @param \Illuminate\Validation\PresenceVerifierInterface|null $presence Database presence verifier
     */
    public function __construct(Factory $factory, ?PresenceVerifierInterface $presence = null)
    {
        $this->presence = $presence;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function getFailures(): array
    {
        // If validator isn't set return empty array
        return $this->validator === null ? [] : $this->validator->getMessageBag()->toArray();
    }

    /**
     * @inheritdoc
     */
    public function validate(array $data, array $rules): bool
    {
        // Doing this to make PHPStan happy
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->factory->make($data, $rules);

        if ($this->presence !== null) {
            // This is unable to be covered as there is no application container in this project
            // @codeCoverageIgnoreStart
            $validator->setPresenceVerifier($this->presence);
            // @codeCoverageIgnoreEnd
        }

        $this->validator = $validator;

        // Add custom rules
        $this->addDependantRule(EmptyWithRule::class);
        $this->addRule(InstanceOfRule::class);

        return $validator->passes();
    }

    /**
     * Add a dependant custom rule to the validator
     *
     * @param string $className The class this rule uses
     *
     * @return void
     */
    private function addDependantRule(string $className): void
    {
        // Pass through to addRule
        $rule = $this->instantiateRule($className);

        // If rule doesn't exist skip, this is only here for safety since method is private
        if ($rule === null) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // Register as dependent extension
        $this->validator->addDependentExtension($rule->getName(), $rule->getRule());
    }

    /**
     * Add a custom rule to the validator
     *
     * @param string $className The class this rule uses
     *
     * @return void
     */
    private function addRule(string $className): void
    {
        // Pass through to addRule
        $rule = $this->instantiateRule($className);

        // If rule doesn't exist skip, this is only here for safety since method is private
        if ($rule === null) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // Register as extension
        $this->validator->addExtension($rule->getName(), $rule->getRule());
    }

    /**
     * Instantiate custom rule and add replacer to validator.
     *
     * @param string $className
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface|null
     */
    private function instantiateRule(string $className): ?ValidationRuleInterface
    {
        // If rule is invalid, skip, this is only here for safety since method is private
        if (\class_exists($className) === false) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        // Instantiate class
        $rule = new $className();

        // If class isn't a valid rule, skip, this is only here for safety since method is private
        if (($rule instanceof ValidationRuleInterface) === false) {
            return null; // @codeCoverageIgnore
        }

        // Register messages
        /**
         * @var \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface $rule
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
        $this->validator->addReplacer($rule->getName(), $rule->getReplacements());

        return $rule;
    }
}
