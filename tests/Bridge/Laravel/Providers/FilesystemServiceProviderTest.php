<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Filesystem;
use EoneoPay\Externals\Bridge\Laravel\Providers\FilesystemServiceProvider;
use EoneoPay\Externals\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\DiskFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\FilesystemInterface;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\FilesystemServiceProvider
 */
class FilesystemServiceProviderTest extends TestCase
{
    /**
     * Test default filesystem is set correctly
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If item is not found in container
     */
    public function testDefaultFilesystem(): void
    {
        // Use cloud as default
        $application = $this->getConfiguredApplication(['default' => 's3']);
        (new FilesystemServiceProvider($application))->register();
        self::assertEquals(
            $application->get(CloudFilesystemInterface::class),
            $application->get(FilesystemInterface::class)
        );

        // Use disk as default
        $application = $this->getConfiguredApplication(['default' => 'local']);
        (new FilesystemServiceProvider($application))->register();
        self::assertEquals(
            $application->get(DiskFilesystemInterface::class),
            $application->get(FilesystemInterface::class)
        );

        // Use custom driver
        $application = $this->getConfiguredApplication(['default' => 'custom']);
        (new FilesystemServiceProvider($application))->register();
        self::assertNotEquals(
            $application->get(CloudFilesystemInterface::class),
            $application->get(FilesystemInterface::class)
        );
        self::assertNotEquals(
            $application->get(DiskFilesystemInterface::class),
            $application->get(FilesystemInterface::class)
        );
    }

    /**
     * Test provider bind filesystem into container.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If item is not found in container
     */
    public function testRegister(): void
    {
        $application = $this->getConfiguredApplication();

        // Run registration
        (new FilesystemServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(
            Filesystem::class,
            $application->get(CloudFilesystemInterface::class)
        );
        self::assertInstanceOf(
            Filesystem::class,
            $application->get(DiskFilesystemInterface::class)
        );
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection Application is nothing else than container */

    /**
     * Create configured application instance
     *
     * @param mixed[]|null $additional Additional configuration entries
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    private function getConfiguredApplication(?array $additional = null): Application
    {
        $application = new ApplicationStub();

        $application->bind('config', static function () use ($additional) {
            return new Config([
                'filesystems' => \array_merge([
                    'disk' => 'local',
                    'cloud' => 's3',
                    'disks' => [
                        'custom' => [
                            'driver' => 'local',
                            'root' => \sprintf('%s/tmp', \sys_get_temp_dir())
                        ],
                        'local' => [
                            'driver' => 'local',
                            'root' => \sys_get_temp_dir()
                        ],
                        's3' => [
                            'driver' => 's3',
                            'key' => null,
                            'secret' => null,
                            'region' => 'us-west-2',
                            'bucket' => null,
                            'url' => null
                        ]
                    ]
                ], $additional ?? [])
            ]);
        });

        return $application;
    }
}
