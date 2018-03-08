<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\EventDispatcher;
use EoneoPay\External\EventDispatcher\Interfaces\EventDispatcherInterface;
use Illuminate\Support\ServiceProvider;

class EventDispatcherServiceProvider extends ServiceProvider
{
    /**
     * Register event dispatcher into Laravel application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);
    }
}
