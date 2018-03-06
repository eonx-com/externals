<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Interfaces;

interface TranslatorInterface
{
    /**
     * Get a value from the language file
     *
     * @param string $key The key to fetch the value for
     * @param string|null $locale The locale to fetch the key from
     *
     * @return string|array|null
     */
    public function get(string $key, ?string $locale = null);
}

