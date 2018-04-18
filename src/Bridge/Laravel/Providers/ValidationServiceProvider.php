<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Validation\CustomRules;
use EoneoPay\External\Bridge\Laravel\Validator;
use EoneoPay\External\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register validator
     *
     * @return void
     */
    public function register(): void
    {
        // Translator is required for error messages
        $this->app->bind(Translator::class, function () {
            return $this->app->make('translator');
        });

        // Interface for validating adhoc objects, depends on translator
        $this->app->bind(ValidatorInterface::class, Validator::class);
    }
}
