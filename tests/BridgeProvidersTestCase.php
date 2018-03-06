<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;
use Illuminate\Container\Container as IlluminateContainer;

abstract class BridgeProvidersTestCase extends DoctrineTestCase
{
    /**
     * @var Application
     */
    private $app;

    /** @noinspection ReturnTypeCanBeDeclaredInspection Application is nothing else than container */

    /**
     * Get Illuminate application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function getApplication()
    {
        if (null !== $this->app) {
            return $this->app;
        }

        $app = new IlluminateContainer();

        // Bind container itself
        $app->bind(IlluminateContainerContract::class, function () use ($app) {
            return $app;
        });

        $this->app = $app;

        return $this->app;
    }
}
