<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Utils\DateTime;
use EoneoPay\Utils\Exceptions\InvalidDateTimeStringException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class AbstractDateConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function initialize(ExecutionContextInterface $context): void
    {
        $this->getInner()->initialize($context);
    }

    /**
     * {@inheritdoc}
     *
     * This validate method is overridden for use in all Date*Validator classes so
     * they can support a string for a date which is not supported by the Symfony
     * Comparison validators.
     *
     * We try to create a DateTime from the provided $value, and if a real datetime
     * is created, we pass it through to the wrapped inner validator to hand off
     * handling to Symfony's comparison validators.
     *
     * These validators do not validate correct date formats, that should be done
     * with the built in DateTime assertion.
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        try {
            $dateValue = new DateTime($value);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (InvalidDateTimeStringException $exception) {
            return;
        }

        $this->getInner()->validate($dateValue, $constraint);
    }

    /**
     * Returns the inner validator.
     *
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    abstract protected function getInner(): ConstraintValidatorInterface;
}
