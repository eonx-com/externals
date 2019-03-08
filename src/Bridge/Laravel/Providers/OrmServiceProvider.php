<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\Bridge\Laravel\ORM\ResolveTargetEntityExtension;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class OrmServiceProvider extends ServiceProvider
{
    /**
     * Register ORM services.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function register(): void
    {
        // Extend Doctrine EntityManager with our EntityManager
        $this->app->extend('em', function (DoctrineEntityManager $entityManager) {
            return new EntityManager($entityManager);
        });

        // Create alias to the package interface for DI purposes
        $this->app->alias('em', EntityManagerInterface::class);

        // Define a ResolveTargetEntityListener
        $this->app->singleton(ResolveTargetEntityListener::class, function (Container $app) {
            $listener = new ResolveTargetEntityListener();

            $replacements = $this->getReplacementsFromApp($app);
            foreach ($replacements as $abstract => $replacement) {
                $listener->addResolveTargetEntity($abstract, $replacement, []);
            }

            return $listener;
        });

        $this->app->singleton(ResolveTargetEntityExtension::class);
    }

    /**
     * Returns any interface replacements defined in doctrine.php under the
     * replacements key.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return string[]
     */
    private function getReplacementsFromApp(Container $app): array
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');

        return $config->get('doctrine.replacements', []);
    }
}
