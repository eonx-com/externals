<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface;
use EoneoPay\Externals\Bridge\Laravel\Validation\EmptyWithRule;
use EoneoPay\Externals\Bridge\Laravel\Validation\InstanceOfRule;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Validation\Factory;

class Validator implements ValidatorInterface
{
    /**
     * Validation factory instance
     *
     * @var \Illuminate\Contracts\Validation\Factory
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
     * @param \Illuminate\Contracts\Validation\Factory $factory Validation factory interface instance
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get messages from the last validation attempt
     *
     * @return mixed[]
     */
    public function getFailures(): array
    {
        // If validator isn't set return empty array
        return $this->validator === null ? [] : $this->validator->getMessageBag()->toArray();
    }

    /**
     * Validate the given data against the provided rules
     *
     * @param mixed[] $data Data to validate
     * @param mixed[] $rules Rules to validate against
     *
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->factory->make($data, $rules);
        // Doing this to make PHPStan happy
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
        /** @var \EoneoPay\Externals\Bridge\Laravel\Interfaces\ValidationRuleInterface $rule */
        $rule = new $className();

        // If class isn't a valid rule, skip, this is only here for safety since method is private
        if (($rule instanceof ValidationRuleInterface) === false) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        // Register messages
        $this->validator->addReplacer($rule->getName(), $rule->getReplacements());

        return $rule;
    }
}
