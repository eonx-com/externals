<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Container;
use EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Eonx\TestUtils\Stubs\Vendor\Illuminate\Container\ContainerStub;
use Eonx\TestUtils\TestCases\Unit\LaravelServiceProviderTestCase;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider
 */
class ContainerServiceProviderTest extends LaravelServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            ContainerInterface::class => Container::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return ContainerServiceProvider::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainer(): ContainerStub
    {
        $container = parent::getContainer();
        $container->instance(IlluminateContainerContract::class, $container);

        return $container;
    }
}
