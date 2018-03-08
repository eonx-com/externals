<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Translator;
use EoneoPay\External\Translator\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as ContractedTranslator;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore Service provider only provides service registration
 */
class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * Register translator
     *
     * @return void
     */
    public function register(): void
    {
        // Tranlator is required for error messages
        $this->app->bind(ContractedTranslator::class, function () {
            return $this->app->make('translator');
        });

        // Interface for translation
        $this->app->bind(TranslatorInterface::class, Translator::class);
    }
}
