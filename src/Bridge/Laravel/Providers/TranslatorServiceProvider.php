<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Translator;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as ContractedTranslator;
use Illuminate\Support\ServiceProvider;

final class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Translator is required for error messages
        $this->app->singleton(ContractedTranslator::class, function () {
            return $this->app->make('translator');
        });

        // Interface for translation
        $this->app->singleton(TranslatorInterface::class, Translator::class);
    }
}
