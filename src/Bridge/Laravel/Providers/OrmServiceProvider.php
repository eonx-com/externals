<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\EntityManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Validation\Factory as  ValidationFactory;

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

        // Bind validation factory interface to current instance for DI purposes
        $this->app->bind(ValidationFactory::class, function (Application $app) {
             return $app->make('validator');
        });
    }
}
