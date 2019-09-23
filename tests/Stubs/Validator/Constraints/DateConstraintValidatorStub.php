<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\AbstractDateConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class DateConstraintValidatorStub extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\ConstraintValidatorInterface $inner
     */
    public function __construct(ConstraintValidatorInterface $inner)
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
