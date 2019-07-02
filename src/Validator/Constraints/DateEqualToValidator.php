<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\EqualToValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateEqualToValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\EqualToValidator
     */
    private $inner;

    /**
     * Constructor
     *
     * @param \Symfony\Component\Validator\Constraints\EqualToValidator $inner
     */
    public function __construct(EqualToValidator $inner)
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
