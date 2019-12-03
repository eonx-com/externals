<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\LessThanValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateLessThanValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\LessThanValidator
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Constraints\LessThanValidator $inner
     */
    public function __construct(LessThanValidator $inner)
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
