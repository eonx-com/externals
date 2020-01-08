<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

final class OrmServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(EntityManagerInterface::class, EntityManager::class);

        // Define a ResolveTargetEntityListener
        $this->app->singleton(ResolveTargetEntityListener::class, static function (Container $app) {
            $listener = new ResolveTargetEntityListener();

            $replacements = $app->make('config')->get('doctrine.replacements') ?? [];
            foreach ($replacements as $abstract => $replacement) {
                $listener->addResolveTargetEntity($abstract, $replacement, []);
            }

            return $listener;
        });
    }
}
