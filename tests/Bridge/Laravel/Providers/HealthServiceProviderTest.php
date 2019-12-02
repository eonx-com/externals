<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HealthServiceProvider;
use EoneoPay\Externals\Health\Health;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use ReflectionClass as ReflectionClass;
use Tests\EoneoPay\Externals\Stubs\Health\HealthCheckStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\HealthServiceProvider
 */
class HealthServiceProviderTest extends TestCase
{
    /**
     * Test that service provider registers the health service into the Laravel application.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();
        $application->tag([HealthCheckStub::class], ['externals_healthcheck']);
        $healthReflection = new ReflectionClass(Health::class);
        $checksProperty = $healthReflection->getProperty('checks');
        $checksProperty->setAccessible(true);

        // Register services
        (new HealthServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(Health::class, $application->get(HealthInterface::class));
        /** @var \EoneoPay\Externals\Health\Interfaces\HealthInterface $health */
        $health = $application->get(HealthInterface::class);
        self::assertCount(1, $checksProperty->getValue($health));
        self::assertInstanceOf(HealthCheckStub::class, $checksProperty->getValue($health)[0]);
    }
}
