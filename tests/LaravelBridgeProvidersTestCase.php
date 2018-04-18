<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use Illuminate\Config\Repository as IlluminateConfig;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcherContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

abstract class LaravelBridgeProvidersTestCase extends DoctrineTestCase
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

        // Bind event dispatcher
        $app->bind(IlluminateDispatcherContract::class, function () use ($app) {
            return new Dispatcher($app);
        });

        $app->bind('config', function () {
            return new IlluminateConfig([
                'filesystems' => [
                    'default' => 'local',
                    'cloud' => 's3',
                    'disks' => [
                        'local' => [
                            'driver' => 'local',
                            'root' => sys_get_temp_dir()
                        ],
                        's3' => [
                            'driver' => 's3',
                            'key' => null,
                            'secret' => null,
                            'region' => 'us-west-2',
                            'bucket' => null,
                            'url' => null
                        ]
                    ]
                ]
            ]);
        });

        // Bind translator
        $app->bind('translator', function () {
            return new Translator(new ArrayLoader(), 'en');
        });

        $this->app = $app;

        return $this->app;
    }
}
