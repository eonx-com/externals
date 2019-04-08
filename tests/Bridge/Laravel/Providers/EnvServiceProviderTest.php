<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\EnvServiceProvider;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\EnvServiceProvider
 */
class EnvServiceProviderTest extends TestCase
{
    /**
     * Test provider bind translator and validator into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Run registration
        (new EnvServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(Env::class, $application->get(EnvInterface::class));
    }
}
