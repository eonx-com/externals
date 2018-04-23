<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Validation\CustomRules;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;
use Illuminate\Support\ServiceProvider;

class EnvServiceProvider extends ServiceProvider
{
    /**
     * Register env
     *
     * @return void
     */
    public function register(): void
    {
        // Interface for getting, setting and removing env values
        $this->app->bind(EnvInterface::class, Env::class);
    }
}
