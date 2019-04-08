<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Container;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Illuminate\Support\ServiceProvider;

final class ContainerServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * @inheritdoc
     */
    public function register(): void
    {
        $this->app->singleton(ContainerInterface::class, Container::class);
    }
}
