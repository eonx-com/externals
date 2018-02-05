<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\EntityManager;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\ServiceProvider;

class OrmServiceProvider extends ServiceProvider
{
    /**
     * Register ORM services.
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

        // Bind validation factory interface to current instance for DI purposes
        $this->app->bind(ValidationFactory::class, function (Container $container) {
            return $container->make('validator');
        });
    }
}
