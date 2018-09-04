<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\FilesystemServiceProvider;
use EoneoPay\Externals\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\DiskFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\FilesystemInterface;
use Illuminate\Config\Repository as Config;
use Tests\EoneoPay\Externals\LaravelBridgeProvidersTestCase;

class FilesystemServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test default filesystem is set correctly
     *
     * @return void
     */
    public function testDefaultFilesystem(): void
    {
        // Use cloud as default
        (new FilesystemServiceProvider($this->getConfiguredApplication(['default' => 's3'])))->register();
        self::assertEquals(
            $this->getApplication()->get(CloudFilesystemInterface::class),
            $this->getApplication()->get(FilesystemInterface::class)
        );

        // Use disk as default
        (new FilesystemServiceProvider($this->getConfiguredApplication(['default' => 'local'])))->register();
        self::assertEquals(
            $this->getApplication()->get(DiskFilesystemInterface::class),
            $this->getApplication()->get(FilesystemInterface::class)
        );

        // Use custom driver
        (new FilesystemServiceProvider($this->getConfiguredApplication(['default' => 'custom'])))->register();
        self::assertNotEquals(
            $this->getApplication()->get(CloudFilesystemInterface::class),
            $this->getApplication()->get(FilesystemInterface::class)
        );
        self::assertNotEquals(
            $this->getApplication()->get(DiskFilesystemInterface::class),
            $this->getApplication()->get(FilesystemInterface::class)
        );
    }

    /**
     * Test provider bind filesystem into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        (new FilesystemServiceProvider($this->getConfiguredApplication()))->register();

        self::assertInstanceOf(
            CloudFilesystemInterface::class,
            $this->getApplication()->get(CloudFilesystemInterface::class)
        );

        self::assertInstanceOf(
            DiskFilesystemInterface::class,
            $this->getApplication()->get(DiskFilesystemInterface::class)
        );
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection Application is nothing else than container */

    /**
     * Create filesystem config
     *
     * @param mixed[]|null $additional Additional configuration entries
     *
     * @return \Illuminate\Contracts\Foundation\Application
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    private function getConfiguredApplication(?array $additional = null)
    {
        $application = $this->getApplication();

        $application->bind('config', function () use ($additional) {
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
