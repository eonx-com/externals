<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\ServiceProvider;

final class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
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
