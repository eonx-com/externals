<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Container;
use EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider;
use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\ContainerServiceProvider
 */
class ContainerServiceProviderTest extends TestCase
{
    /**
     * Test provider register container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Bind application container
        $application->bind(IlluminateContainerContract::class, static function () use ($application) {
            return $application;
        });

        // Run registration
        (new ContainerServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(Container::class, $application->get(ContainerInterface::class));
    }
}
