<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Symfony\Validator\Interfaces;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidatorInterface
{
    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * If no constraint is passed, the constraint
     * {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
     *
     * The validation groups can be provided as an array of strings or a
     * GroupSequence that will be used to validate. If none is given,
     * "Default" is assumed
     *
     * @phpstan-return \Symfony\Component\Validator\ConstraintViolationListInterface<\Symfony\Component\Validator\ConstraintViolationInterface>
     *
     * @param mixed $value The value to validate
     * @param \Symfony\Component\Validator\Constraint[] $constraints The constraint(s) to validate against
     * @param string[]|\Symfony\Component\Validator\Constraints\GroupSequence|null $groups
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate($value, ?array $constraints = null, $groups = null): ConstraintViolationListInterface;
}
