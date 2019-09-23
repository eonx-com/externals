<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\Filter;
use EoneoPay\Externals\Validator\Constraints\FilterValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\FilterValidator
 */
class FilterValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test bails on empty value.
     *
     * @return void
     */
    public function testEmptyValue(): void
    {
        $constraint = new Filter();

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate(null, $constraint);

        self::assertCount(0, $context->getViolations());
    }

    /**
     * Test filter int when it is a hex string.
     *
     * @return void
     */
    public function testFilterHexIntFlags(): void
    {
        $constraint = new Filter([
            'filter' => 'FILTER_VALIDATE_INT',
            'flags' => ['FILTER_FLAG_ALLOW_HEX'],
        ]);

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate('0x5a', $constraint);

        self::assertCount(0, $context->getViolations());
    }

    /**
     * Test filter int.
     *
     * @return void
     */
    public function testFilterInt(): void
    {
        $constraint = new Filter(['filter' => 'FILTER_VALIDATE_INT']);

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate(5, $constraint);

        self::assertCount(0, $context->getViolations());
    }

    /**
     * Test filter int when it is a non int string.
     *
     * @return void
     */
    public function testFilterStringInt(): void
    {
        $constraint = new Filter(['filter' => 'FILTER_VALIDATE_INT']);
        $expectedViolation = new ConstraintViolation(
            'This value is not valid.',
            'This value is not valid.',
            [],
            'root',
            '',
            'purple',
            null,
            null,
            $constraint
        );

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate('purple', $constraint);

        self::assertCount(1, $context->getViolations());
        self::assertEquals($expectedViolation, $context->getViolations()[0]);
    }

    /**
     * Test invalid constraint passed.
     *
     * @return void
     */
    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "EoneoPay\Externals\Validator\Constraints\Filter", "Symfony\Component\Validator\Constraints\NotBlank" given'); // phpcs:ignore

        $validator = $this->getValidatorInstance();
        $validator->validate(null, new NotBlank());
    }

    /**
     * Get the validator under test.
     *
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *
     * @return \EoneoPay\Externals\Validator\Constraints\FilterValidator
     */
    private function getValidatorInstance(?ExecutionContextInterface $context = null): FilterValidator
    {
        $validator = new FilterValidator();

        if ($context !== null) {
            $validator->initialize($context);
        }

        return $validator;
    }
}
