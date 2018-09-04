<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\EnvServiceProvider;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\EnvServiceProvider
 */
class EnvServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider bind translator and validator into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        (new EnvServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(Env::class, $this->getApplication()->get(EnvInterface::class));
    }
}
