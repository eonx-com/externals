<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Request;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
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
