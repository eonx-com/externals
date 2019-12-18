<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface;
use Illuminate\Support\ServiceProvider;

final class EventDispatcherServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(EventDispatcherInterface::class, EventDispatcher::class);
    }
}
