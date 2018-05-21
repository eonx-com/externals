<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\MockInterface;

abstract class ExtensionsTestCase extends TestCase
{
    /**
     * Get mock for Doctrine Entity Manager.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mockEntityManager(): MockInterface
    {
        return Mockery::mock(EntityManagerInterface::class);
    }

    /**
     * Get mock for Doctrine Event Manager.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mockEventManager(): MockInterface
    {
        return Mockery::mock(EventManager::class);
    }
}
