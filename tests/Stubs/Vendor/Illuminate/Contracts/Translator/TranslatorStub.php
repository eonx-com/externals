<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Translator;

use Illuminate\Contracts\Translation\Translator;

class TranslatorStub implements Translator
{
    /**
     * {@inheritdoc}
     */
    public function get($key, array $replace = [], $locale = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale): void
    {
    }
}
