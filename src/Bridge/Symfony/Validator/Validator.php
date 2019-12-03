<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Symfony\Validator;

use EoneoPay\Externals\Bridge\Symfony\Validator\Interfaces\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $inner;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $inner
     */
    public function __construct(SymfonyValidatorInterface $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, ?array $constraints = null, $groups = null): ConstraintViolationListInterface
    {
        return $this->inner->validate($value, $constraints, $groups);
    }
}
