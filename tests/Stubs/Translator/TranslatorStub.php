<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Translator;

use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;

/**
 * @coversNothing
 */
class TranslatorStub implements TranslatorInterface
{
    /**
     * @inheritdoc
     */
    public function get(string $key, ?array $replace = null, ?string $locale = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function trans(string $key, ?array $replace = null, ?string $locale = null): ?string
    {
        return '';
    }
}
