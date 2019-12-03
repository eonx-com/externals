<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConstraintValidatorStub implements ConstraintValidatorInterface
{
    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface[]
     */
    private $initialized;

    /**
     * The calls to validate().
     *
     * @var mixed[]
     */
    private $validated = [];

    /**
     * Returns any initialized calls.
     *
     * @return \Symfony\Component\Validator\Context\ExecutionContextInterface[]
     */
    public function getInitialized(): array
    {
        return $this->initialized;
    }

    /**
     * Returns calls to validate().
     *
     * @return mixed[]
     */
    public function getValidated(): array
    {
        return $this->validated;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExecutionContextInterface $context): void
    {
        $this->initialized[] = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        $this->validated[] = \compact('value', 'constraint');
    }
}
