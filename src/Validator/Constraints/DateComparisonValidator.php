<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Utils\DateTime;
use EoneoPay\Utils\Exceptions\InvalidDateTimeStringException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DateComparisonValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Constructor
     *
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $expressionLanguage
     */
    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof DateComparison !== true) {
            throw new UnexpectedTypeException($constraint, DateComparison::class);
        }

        if ($value === null) {
            // Dont validate empty values.

            return;
        }

        /**
         * @var \EoneoPay\Externals\Validator\Constraints\DateComparison $constraint
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */

        try {
            $dateValue = new DateTime($value);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (InvalidDateTimeStringException $exception) {
            $dateValue = null;
        }

        if ($dateValue === null) {
            // Failed to parse the datetime value, let other validators handle validity

            return;
        }

        $result = $this->expressionLanguage->evaluate($constraint->expr, [
            'value' => $dateValue,
            'this' => $this->context->getObject()
        ]);

        if ($result !== true) {
            $this->context->buildViolation($constraint->message)
                ->setParameter(
                    '{{ value }}',
                    $this->formatValue($dateValue, self::PRETTY_DATE)
                )
                ->addViolation();
        }
    }
}
