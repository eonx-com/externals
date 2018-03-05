<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Interfaces\ValidatorInterface;
use EoneoPay\External\Bridge\Laravel\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

/**
 * @codeCoverageIgnore Service provider only provides service registration
 */
class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register validator
     *
     * @return void
     */
    public function register(): void
    {
        // Tranlator is required for error messages
        $this->app->bind(Translator::class, function () {
            return $this->app->make('translator');
        });

        // Interface for validating adhoc objects, depends on translator
        $this->app->bind(ValidatorInterface::class, Validator::class);
    }
}
