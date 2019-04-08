<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation;

use ArrayAccess;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

class ApplicationStub implements Application, ArrayAccess
{
    /**
     * Container bindings
     *
     * @var \Illuminate\Container\Container
     */
    private $container;

    /**
     * Create container
     */
    public function __construct()
    {
        $this->container = new Container();
    }

    /**
     * @inheritdoc
     */
    public function addContextualBinding($concrete, $abstract, $implementation): void
    {
    }

    /**
     * @inheritdoc
     */
    public function afterResolving($abstract, Closure $callback = null): void
    {
    }

    /**
     * @inheritdoc
     */
    public function alias($abstract, $alias)
    {
        return $this->callMethod('alias', [$abstract, $alias]);
    }

    /**
     * @inheritdoc
     */
    public function basePath()
    {
    }

    /**
     * @inheritdoc
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        return $this->callMethod('bind', [$abstract, $concrete, $shared]);
    }

    /**
     * @inheritdoc
     */
    public function bindIf($abstract, $concrete = null, $shared = false): void
    {
    }

    /**
     * @inheritdoc
     */
    public function boot(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function booted($callback): void
    {
    }

    /**
     * @inheritdoc
     */
    public function booting($callback): void
    {
    }

    /**
     * @inheritdoc
     */
    public function bootstrapPath($path = '')
    {
    }

    /**
     * @inheritdoc
     */
    public function bootstrapWith(array $bootstrappers): void
    {
    }

    /**
     * @inheritdoc
     */
    public function bound($abstract)
    {
    }

    /**
     * @inheritdoc
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function configPath($path = '')
    {
    }

    /**
     * @inheritdoc
     */
    public function configurationIsCached()
    {
    }

    /**
     * @inheritdoc
     */
    public function databasePath($path = '')
    {
    }

    /**
     * @inheritdoc
     */
    public function detectEnvironment(Closure $callback)
    {
    }

    /**
     * @inheritdoc
     */
    public function environment(...$environments)
    {
    }

    /**
     * @inheritdoc
     */
    public function environmentFile()
    {
    }

    /**
     * @inheritdoc
     */
    public function environmentFilePath()
    {
    }

    /**
     * @inheritdoc
     */
    public function environmentPath()
    {
    }

    /**
     * @inheritdoc
     */
    public function extend($abstract, Closure $closure)
    {
        return $this->callMethod('extend', [$abstract, $closure]);
    }

    /**
     * @inheritdoc
     */
    public function factory($abstract)
    {
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->callMethod('get', [$id]);
    }

    /**
     * @inheritdoc
     */
    public function getCachedConfigPath()
    {
    }

    /**
     * @inheritdoc
     */
    public function getCachedPackagesPath()
    {
    }

    /**
     * @inheritdoc
     */
    public function getCachedRoutesPath()
    {
    }

    /**
     * @inheritdoc
     */
    public function getCachedServicesPath()
    {
    }

    /**
     * @inheritdoc
     */
    public function getLocale()
    {
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
    }

    /**
     * @inheritdoc
     */
    public function getProviders($provider)
    {
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
    }

    /**
     * @inheritdoc
     */
    public function hasBeenBootstrapped()
    {
    }

    /**
     * @inheritdoc
     */
    public function instance($abstract, $instance)
    {
        return $this->callMethod('instance', [$abstract, $instance]);
    }

    /**
     * @inheritdoc
     */
    public function isDownForMaintenance()
    {
    }

    /**
     * @inheritdoc
     */
    public function loadDeferredProviders(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function loadEnvironmentFrom($file)
    {
    }

    /**
     * @inheritdoc
     */
    public function make($abstract, array $parameters = [])
    {
        return $this->callMethod('make', [$abstract, $parameters]);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->callMethod('make', [$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value): void
    {
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
    }

    /**
     * @inheritdoc
     */
    public function register($provider, $force = false)
    {
    }

    /**
     * @inheritdoc
     */
    public function registerConfiguredProviders(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function registerDeferredProvider($provider, $service = null): void
    {
    }

    /**
     * @inheritdoc
     */
    public function resolveProvider($provider)
    {
    }

    /**
     * @inheritdoc
     */
    public function resolved($abstract)
    {
    }

    /**
     * @inheritdoc
     */
    public function resolving($abstract, Closure $callback = null): void
    {
    }

    /**
     * @inheritdoc
     */
    public function resourcePath($path = '')
    {
    }

    /**
     * @inheritdoc
     */
    public function routesAreCached()
    {
    }

    /**
     * @inheritdoc
     */
    public function runningInConsole()
    {
    }

    /**
     * @inheritdoc
     */
    public function runningUnitTests()
    {
    }

    /**
     * @inheritdoc
     */
    public function setLocale($locale): void
    {
    }

    /**
     * @inheritdoc
     */
    public function shouldSkipMiddleware()
    {
    }

    /**
     * @inheritdoc
     */
    public function singleton($abstract, $concrete = null)
    {
        return $this->callMethod('singleton', [$abstract, $concrete]);
    }

    /**
     * @inheritdoc
     */
    public function storagePath()
    {
    }

    /**
     * @inheritdoc
     */
    public function tag($abstracts, $tags): void
    {
    }

    /**
     * @inheritdoc
     */
    public function tagged($tag)
    {
    }

    /**
     * @inheritdoc
     */
    public function terminate(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function version()
    {
    }

    /**
     * @inheritdoc
     */
    public function when($concrete)
    {
    }

    /**
     * Call a container method
     *
     * @param string $method The method to call
     * @param mixed[]|null $parameters Parameters to pass to the method
     *
     * @return mixed
     */
    private function callMethod(string $method, ?array $parameters = null)
    {
        try {
            return \call_user_func_array([$this->container, $method], $parameters ?? []);
        } catch (BindingResolutionException $exception) {
            return false;
        }
    }
}
