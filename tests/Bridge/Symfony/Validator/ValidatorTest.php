<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Symfony\Validator;

use EoneoPay\Externals\Bridge\Symfony\Validator\Validator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Validator\ValidatorStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Symfony\Validator\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * Tests validate calls the inner validator.
     *
     * @return void
     */
    public function testValidate(): void
    {
        $constraint = new NotBlank();

        $expectedCall = [
            'value' => 'value',
            'constraints' => [$constraint],
            'groups' => ['group'],
        ];

        $innerValidator = new ValidatorStub();
        $validator = $this->getValidator($innerValidator);

        $validator->validate('value', [$constraint], ['group']);

        self::assertCount(1, $innerValidator->getCalls());
        self::assertSame($expectedCall, $innerValidator->getCalls()[0]);
    }

    /**
     * Returns the validator under test.
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return \EoneoPay\Externals\Bridge\Symfony\Validator\Validator
     */
    private function getValidator(ValidatorInterface $validator): Validator
    {
        return new Validator($validator);
    }
}
