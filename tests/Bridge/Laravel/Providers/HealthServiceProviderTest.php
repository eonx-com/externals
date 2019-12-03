<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HealthServiceProvider;
use EoneoPay\Externals\Health\Health;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
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
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();
        $healthCheck = new HealthCheckStub('Test Check', 'test-check');
        $application->instance(HealthCheckStub::class, $healthCheck);
        $application->tag(HealthCheckStub::class, ['externals_healthcheck']);

        // Register services
        (new HealthServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(Health::class, $application->get(HealthInterface::class));
        /** @var \EoneoPay\Externals\Health\Interfaces\HealthInterface $health */
        $health = $application->get(HealthInterface::class);
        $result = $health->extended();
        self::assertCount(1, $result->getServices());
        self::assertArrayHasKey('test-check', $result->getServices());
    }
}
