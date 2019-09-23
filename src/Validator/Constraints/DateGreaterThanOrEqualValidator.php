<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateGreaterThanOrEqualValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator $inner
     */
    public function __construct(GreaterThanOrEqualValidator $inner)
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
