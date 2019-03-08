<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\Bridge\Laravel\Providers\OrmServiceProvider;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Config\Repository;
use stdClass;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class OrmServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider extend entity manager in container using our entity manager.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $app = $this->getApplication();

        $app->singleton('em', function () {
            return $this->getDoctrineEntityManager();
        });
        $app->instance('config', new Repository([
            'doctrine' => [
                'replacements' => [
                    stdClass::class => 'replacement'
                ]
            ]
        ]));

        (new OrmServiceProvider($app))->register();

        self::assertInstanceOf(EntityManagerInterface::class, $app->get('em'));

        // ensure the ResolveTargetEntityListener fires
        $app->make(ResolveTargetEntityListener::class);
    }
}
