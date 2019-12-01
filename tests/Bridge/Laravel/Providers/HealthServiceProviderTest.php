<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\HealthServiceProvider;
use EoneoPay\Externals\Health\Health;
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

        // Bind illuminate translator to key
        $application->bind('health', static function () {
            return new Health([]);
        });

        // Register services
        (new HealthServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(Health::class, $application->get('health'));
    }
}
