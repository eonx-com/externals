<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validator\Constraints;

use EoneoPay\Externals\Validator\Constraints\DateComparison;
use EoneoPay\Externals\Validator\Constraints\DateComparisonValidator;
use EoneoPay\Utils\DateTime;
use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Tests\EoneoPay\Externals\TestCases\ValidatorConstraintTestCase;

/**
 * @covers \EoneoPay\Externals\Validator\Constraints\DateComparisonValidator
 */
class DateComparisonValidatorTest extends ValidatorConstraintTestCase
{
    /**
     * Test bails on empty value
     *
     * @return void
     */
    public function testEmptyValue(): void
    {
        $constraint = new DateComparison();

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate(null, $constraint);

        static::assertCount(0, $context->getViolations());
    }

    /**
     * Test bails on invalid date value
     *
     * @return void
     */
    public function testInvalidDate(): void
    {
        $constraint = new DateComparison();

        $context = $this->buildContext($constraint);
        $validator = $this->getValidatorInstance($context);

        $validator->validate('purple elephant', $constraint);

        static::assertCount(0, $context->getViolations());
    }

    /**
     * Test valid
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testValidValue(): void
    {
        $dateToValidate = '2019-01-02T01:02:03Z';
        $constraint = new DateComparison('value < this.compare');

        $context = $this->buildContext($constraint);
        $object = new stdClass();
        $object->compare = new DateTime('2019-02-03T04:05:06Z');
        $context->setNode($dateToValidate, $object, null, '');

        $validator = $this->getValidatorInstance($context);

        $validator->validate($dateToValidate, $constraint);

        static::assertCount(0, $context->getViolations());
    }

    /**
     * Test invalid
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testInvalidValue(): void
    {
        $oldLocale = \locale_get_default();
        \locale_set_default('en-AU');

        $dateToValidate = '2019-01-02T01:02:03Z';
        $constraint = new DateComparison('value > this.compare');
        $expectedViolation = new ConstraintViolation(
            'The date comparison failed.',
            'The date comparison failed.',
            [
                '{{ value }}' => '2 Jan 2019, 1:02 am'
            ],
            'root',
            '',
            '2019-01-02T01:02:03Z',
            null,
            null,
            $constraint
        );

        $context = $this->buildContext($constraint);
        $object = new stdClass();
        $object->compare = new DateTime('2019-02-03T04:05:06Z');
        $context->setNode($dateToValidate, $object, null, '');

        $validator = $this->getValidatorInstance($context);

        $validator->validate($dateToValidate, $constraint);

        \locale_set_default($oldLocale);

        static::assertCount(1, $context->getViolations());
        static::assertEquals($expectedViolation, $context->getViolations()[0]);
    }

    /**
     * Tests an invalid constraint
     *
     * @return void
     */
    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "EoneoPay\Externals\Validator\Constraints\DateComparison", "Symfony\Component\Validator\Constraints\NotBlank" given'); // phpcs:ignore

        $validator = $this->getValidatorInstance();
        $validator->validate(null, new NotBlank());
    }

    /**
     * Returns the validator under test.
     *
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *
     * @return \EoneoPay\Externals\Validator\Constraints\DateComparisonValidator
     */
    private function getValidatorInstance(?ExecutionContextInterface $context = null): DateComparisonValidator
    {
        $expressionLanguage = new ExpressionLanguage();

        $validator = new DateComparisonValidator($expressionLanguage);

        if ($context !== null) {
            $validator->initialize($context);
        }

        return $validator;
    }
}
