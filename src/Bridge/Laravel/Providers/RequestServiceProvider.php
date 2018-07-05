<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Request;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Register http request
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Trusted proxies must be set statically
     */
    public function register(): void
    {
        // Interface for incoming http requests
        $this->app->singleton(RequestInterface::class, function () {
            // Create env instance
            $env = $this->app->make(Env::class);

            // Set proxy list
            HttpRequest::setTrustedProxies(
                \explode(',', $env->get('TRUSTED_PROXIES') ?? ''),
                HttpRequest::HEADER_X_FORWARDED_ALL
            );

            return $this->app->make(Request::class);
        });
    }
}
