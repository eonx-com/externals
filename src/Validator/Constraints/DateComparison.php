<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Run a filter_var call on a property.
 *
 * @Annotation
 *
 * @Target({"METHOD", "PROPERTY"})
 */
final class DateComparison extends Constraint
{
    /**
     * The expression to use when comparing the dates
     *
     * @var string
     */
    public $expr;

    /**
     * The message to return when the comparison fails.
     *
     * @var string
     */
    public $message = 'The date comparison failed.';

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'expr';
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
