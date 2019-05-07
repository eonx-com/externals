<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use Tests\EoneoPay\Externals\Stubs\Auth\AuthStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Auth\AuthStub as FactoryStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Auth
 */
class AuthTest extends TestCase
{
    /**
     * Test auth passthrough to factory
     *
     * @return void
     */
    public function testAuthPassthrough(): void
    {
        $factory = new FactoryStub();
        $auth = new AuthStub(null, $factory);

        $auth->setDefaultDriver('test');
        self::assertSame('test', $factory->getDefaultDriver());

        $auth->guard('testGuard');
        self::assertSame(['testGuard'], $factory->getGuards());

        $auth->shouldUse('testUse');
        self::assertSame(['testUse'], $factory->getUses());
    }
}
