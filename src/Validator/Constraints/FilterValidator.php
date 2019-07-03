<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class FilterValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof Filter !== true) {
            throw new UnexpectedTypeException($constraint, Filter::class);
        }

        if ($value === null || $value === '') {
            // Dont validate empty values.

            return;
        }

        /**
         * @var \EoneoPay\Externals\Validator\Constraints\Filter $constraint
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */

        $filter = \constant($constraint->filter);
        $flags = 0;
        foreach ($constraint->flags ?? [] as $flag) {
            $flags |= \constant($flag) ?? 0;
        }

        $result = \filter_var($value, $filter, ['flags' => $flags]);

        if ($result === false) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
