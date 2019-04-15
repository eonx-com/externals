<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;
use Illuminate\Support\ServiceProvider;

final class EnvServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Interface for getting, setting and removing env values
        $this->app->singleton(EnvInterface::class, Env::class);
    }
}
