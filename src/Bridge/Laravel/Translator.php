<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Bridge\Laravel\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as ContractedTranslator;

class Translator implements TranslatorInterface
{
    /**
     * Contracted translator instance
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    private $translator;

    /**
     * Create new validation instance
     *
     * @param \Illuminate\Contracts\Translation\Translator $translator Contracted translator instance
     */
    public function __construct(ContractedTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get a value from the language file
     *
     * @param string $key The key to fetch the message for
     * @param array|null $replace Attributes to replace within the message
     * @param string|null $locale The locale to fetch the key from
     *
     * @return string|array|null
     */
    public function get(string $key, ?array $replace = null, ?string $locale = null)
    {
        return $this->translator->trans($key, $replace ?? [], $locale);
    }
}
