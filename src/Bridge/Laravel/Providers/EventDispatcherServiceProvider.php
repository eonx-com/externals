<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\EventDispatcher;
use Illuminate\Support\ServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

final class EventDispatcherServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(PsrEventDispatcherInterface::class, EventDispatcher::class);
    }
}
