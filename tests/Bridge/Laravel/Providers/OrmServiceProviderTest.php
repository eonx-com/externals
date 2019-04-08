<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\Bridge\Laravel\Providers\OrmServiceProvider;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Config\Repository;
use stdClass;
use Tests\EoneoPay\Externals\ORMTestCase;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\OrmServiceProvider
 */
class OrmServiceProviderTest extends ORMTestCase
{
    /**
     * Test provider extend entity manager in container using our entity manager.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If item isn't found in container
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Bind doctrine entity manager to key
        $application->singleton('em', function () {
            return $this->getDoctrineEntityManager();
        });

        // Configure application
        $application->instance('config', new Repository([
            'doctrine' => [
                'replacements' => [
                    stdClass::class => 'replacement'
                ]
            ]
        ]));

        // Run service provider
        (new OrmServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(EntityManagerInterface::class, $application->get('em'));

        // Ensure the ResolveTargetEntityListener fires
        $application->make(ResolveTargetEntityListener::class);
    }
}
