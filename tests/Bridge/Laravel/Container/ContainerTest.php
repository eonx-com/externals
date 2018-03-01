<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Container;

use EoneoPay\External\Bridge\Laravel\Container\Container;
use Illuminate\Container\Container as IlluminateContainer;
use Psr\Container\NotFoundExceptionInterface;
use Tests\EoneoPay\External\Bridge\Laravel\Stubs\ServiceStub;
use Tests\EoneoPay\External\TestCase;

class ContainerTest extends TestCase
{
    /**
     * Container should use illuminate container to retrieve services.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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

    /**
     * Container should throw PSR exception if service not found.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testServiceNotFoundException(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        (new Container(new IlluminateContainer()))->get(ServiceStub::class);
    }
}
