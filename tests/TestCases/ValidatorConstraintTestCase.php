<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\TestCases;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Translator\TranslatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Validator\ValidatorStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @coversNothing
 */
class ValidatorConstraintTestCase extends TestCase
{
    /**
     * Builds a fake ExecutionContext.
     *
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @return \Symfony\Component\Validator\Context\ExecutionContextInterface
     */
    protected function buildContext(Constraint $constraint): ExecutionContextInterface
    {
        $validator = new ValidatorStub();
        $translator = new TranslatorStub();

        $context = new ExecutionContext(
            $validator,
            'root',
            $translator
        );

        $context->setConstraint($constraint);

        return $context;
    }
}
