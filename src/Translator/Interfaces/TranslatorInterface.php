<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Translator\Interfaces;

interface TranslatorInterface
{
    /**
     * Get a value from the language file
     *
     * @param string $key The key to fetch the message for
     * @param mixed[]|null $replace Attributes to replace within the message
     * @param string|null $locale The locale to fetch the key from
     *
     * @return string|array|null
     */
    public function get(string $key, ?array $replace = null, ?string $locale = null);

    /**
     * Get a value from the language file and ensure a string is always returned
     *
     * @param string $key The key to fetch the message for
     * @param mixed[]|null $replace Attributes to replace within the message
     * @param string|null $locale The locale to fetch the key from
     *
     * @return string|null
     */
    public function trans(string $key, ?array $replace = null, ?string $locale = null): ?string;
}
