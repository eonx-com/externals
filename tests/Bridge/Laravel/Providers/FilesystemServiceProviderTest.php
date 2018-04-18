<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\FilesystemServiceProvider;
use EoneoPay\Externals\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\DiskFilesystemInterface;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class FilesystemServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test provider bind filesystem into container.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        (new FilesystemServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(
            CloudFilesystemInterface::class,
            $this->getApplication()->get(CloudFilesystemInterface::class)
        );

        self::assertInstanceOf(
            DiskFilesystemInterface::class,
            $this->getApplication()->get(DiskFilesystemInterface::class)
        );
    }
}
