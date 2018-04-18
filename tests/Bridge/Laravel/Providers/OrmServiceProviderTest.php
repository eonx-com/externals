<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\OrmServiceProvider;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class OrmServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider extend entity manager in container using our entity manager.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        $this->getApplication()->singleton('em', function () {
            return $this->getDoctrineEntityManager();
        });

        (new OrmServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(EntityManagerInterface::class, $this->getApplication()->get('em'));
    }
}
