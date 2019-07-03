<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Validator;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversNothing
 */
class ValidatorStub implements ValidatorInterface
{
    /**
     * The calls to validate()
     *
     * @var mixed[]
     */
    private $calls = [];

    /**
     * @var \Symfony\Component\Validator\ConstraintViolation[][]
     */
    private $violations;

    /**
     * Constructor
     *
     * @param \Symfony\Component\Validator\ConstraintViolation[][]|null $violations
     */
    public function __construct(?array $violations = null)
    {
        $this->violations = $violations ?? [];
    }

    /**
     * Returns validate calls.
     *
     * @return mixed[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function inContext(ExecutionContextInterface $context)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startContext()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        $this->calls[] = \compact('value', 'constraints', 'groups');

        return new ConstraintViolationList(\array_shift($this->violations) ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function validateProperty($object, $propertyName, $groups = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
    }
}
