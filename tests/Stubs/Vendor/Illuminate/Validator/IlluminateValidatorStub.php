<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Validator;

use EoneoPay\Externals\Bridge\Laravel\IlluminateValidator;

class IlluminateValidatorStub extends IlluminateValidator
{
    /**
     * {@inheritdoc}
     */
    public function getCacheKey($rule): string
    {
        return parent::getCacheKey($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedRule($rule): array
    {
        return parent::getParsedRule($rule);
    }
}
