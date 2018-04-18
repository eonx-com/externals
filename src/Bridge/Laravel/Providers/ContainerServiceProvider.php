<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Container;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Illuminate\Support\ServiceProvider;

class ContainerServiceProvider extends ServiceProvider
{
    /**
     * Register PSR11 Container implementation in Laravel/Lumen application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ContainerInterface::class, Container::class);
    }
}
