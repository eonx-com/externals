<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotEqualToValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class DateNotEqualToValidator extends AbstractDateConstraintValidator
{
    /**
     * @var \Symfony\Component\Validator\Constraints\NotEqualToValidator
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Constraints\NotEqualToValidator $inner
     */
    public function __construct(NotEqualToValidator $inner)
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
