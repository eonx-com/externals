<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\LessThanOrEqualValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateLessThanOrEqualValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\LessThanOrEqualValidator
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Constraints\LessThanOrEqualValidator $inner
     */
    public function __construct(LessThanOrEqualValidator $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     */
    protected function getInner(): ConstraintValidatorInterface
    {
        return $this->inner;
    }
}
