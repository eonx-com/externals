<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Container;
use Illuminate\Container\Container as IlluminateContainer;
use Psr\Container\NotFoundExceptionInterface;
use Tests\EoneoPay\Externals\Bridge\Laravel\Stubs\ServiceStub;
use Tests\EoneoPay\Externals\TestCase;

class ContainerTest extends TestCase
{
    /**
     * Container should use illuminate container to retrieve services.
     *
     * @return void
     */
    public function testContainerForIlluminate(): void
    {
        $illuminate = new IlluminateContainer();
        $illuminate->singleton(ServiceStub::class, ServiceStub::class);

        $container = new Container($illuminate);

        self::assertTrue($container->has(ServiceStub::class));
        self::assertFalse($container->has('invalid'));

        self::assertInstanceOf(ServiceStub::class, $container->get(ServiceStub::class));
    }
}
