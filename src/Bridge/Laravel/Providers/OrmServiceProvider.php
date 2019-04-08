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

final class OrmServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * @inheritdoc
     */
    public function register(): void
    {
        // Extend Doctrine EntityManager with our EntityManager
        $this->app->extend('em', static function (DoctrineEntityManager $entityManager) {
            return new EntityManager($entityManager);
        });

        // Create alias to the package interface for DI purposes
        $this->app->alias('em', EntityManagerInterface::class);

        // Define a ResolveTargetEntityListener
        $this->app->singleton(ResolveTargetEntityListener::class, static function (Container $app) {
            $listener = new ResolveTargetEntityListener();

            $replacements = $app->make('config')->get('doctrine.replacements') ?? [];
            foreach ($replacements as $abstract => $replacement) {
                $listener->addResolveTargetEntity($abstract, $replacement, []);
            }

            return $listener;
        });

        $this->app->singleton(ResolveTargetEntityExtension::class);
    }
}
