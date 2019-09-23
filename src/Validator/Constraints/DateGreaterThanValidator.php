<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThanValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateGreaterThanValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\GreaterThanValidator
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Constraints\GreaterThanValidator $inner
     */
    public function __construct(GreaterThanValidator $inner)
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
