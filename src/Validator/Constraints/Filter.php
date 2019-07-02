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
final class Filter extends Constraint
{
    /**
     * The FILTER_VALIDATE constant to be used.
     *
     * @var string
     */
    public $filter;

    /**
     * An array of FILTER_FLAG constants to be used.
     *
     * @var string[]|null
     */
    public $flags;

    /**
     * The error message to be presented.
     *
     * @var string
     */
    public $message = 'This value is not valid.';

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'filter';
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
