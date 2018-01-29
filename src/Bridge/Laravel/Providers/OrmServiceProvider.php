<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\EntityManager;
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
        $this->app->extend('em', function (DoctrineEntityManager $entityManager) {
            return new EntityManager($entityManager);
        });
    }
}
