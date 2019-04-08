<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

final class Translator implements TranslatorInterface
{
    /**
     * Contracted translator instance
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    private $translator;

    /**
     * Create new translation instance
     *
     * @param \Illuminate\Contracts\Translation\Translator $contract Contracted translator instance
     */
    public function __construct(TranslatorContract $contract)
    {
        $this->translator = $contract;
    }

    /**
     * @inheritdoc
     */
    public function get(string $key, ?array $replace = null, ?string $locale = null)
    {
        return $this->translator->trans($key, $replace ?? [], $locale);
    }

    /**
     * @inheritdoc
     */
    public function trans(string $key, ?array $replace = null, ?string $locale = null): ?string
    {
        $translated = $this->get($key, $replace ?? [], $locale);

        return \is_array($translated) ? \implode(', ', $translated) : $translated;
    }
}
