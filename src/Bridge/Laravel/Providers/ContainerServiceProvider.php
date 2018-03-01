<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Container\Container;
use EoneoPay\External\Container\Interfaces\ContainerInterface;
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
