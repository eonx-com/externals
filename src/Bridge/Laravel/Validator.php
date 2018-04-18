<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Externals\Bridge\Laravel\Validation\EmptyWithRule;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Validation\Factory;

class Validator implements ValidatorInterface
{
    /**
     * Validation factory instance
     *
     * @var \Illuminate\Validation\Factory
     */
    private $factory;

    /**
     * Validation instance
     *
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Create new validation instance
     *
     * @param \Illuminate\Validation\Factory $factory Validation factory instance
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get messages from the last validation attempt
     *
     * @return array
     */
    public function getFailures(): array
    {
        // If validator isn't set return empty array
        return null === $this->validator ? [] : $this->validator->getMessageBag()->toArray();
    }

    /**
     * Validate the given data against the provided rules
     *
     * @param array $data Data to validate
     * @param array $rules Rules to validate against
     *
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        // Create validator
        $this->validator = $this->factory->make($data, $rules);

        // Add custom rules
        $this->addDependantRule(EmptyWithRule::class);

        return $this->validator->passes();
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
        $rule = $this->addRule($className);

        // If rule doesn't exist skip, this is only here for safety since method is private
        if (null === $rule) {
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
     * @return \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface|null
     */
    private function addRule(string $className): ?ValidationRuleInterface
    {
        // If rule is invalid, skip, this is only here for safety since method is private
        if (false === \class_exists($className)) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        // Instantiate class
        /** @var \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface $rule */
        $rule = new $className();

        // If class isn't a valid rule, skip, this is only here for safety since method is private
        if (!$rule instanceof ValidationRuleInterface) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        // Register messages
        $this->validator->addReplacer($rule->getName(), $rule->getReplacements());

        return $rule;
    }
}
