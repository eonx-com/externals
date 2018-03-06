<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Interfaces\RequestInterface;
use EoneoPay\External\Bridge\Laravel\Request;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Register http request
     *
     * @return void
     */
    public function register(): void
    {
        // Interface for incoming http requests
        $this->app->bind(RequestInterface::class, Request::class);
    }
}
