<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Health\Health;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(HealthInterface::class, static function (Container $app): HealthInterface {
            $tagged = $app->tagged('externals_healthcheck');
            $checks = \is_array($tagged) ? $tagged : (array)$tagged;

            return new Health($checks);
        });
    }
}
