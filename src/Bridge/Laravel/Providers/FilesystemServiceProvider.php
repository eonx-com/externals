<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Filesystem;
use EoneoPay\Externals\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\DiskFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\FilesystemInterface;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register filesystems
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerManager();

        // Interface for cloud-based filesystem
        if ($this->driverExists('cloud')) {
            $this->app->bind(CloudFilesystemInterface::class, function () {
                return new Filesystem($this->app->make('filesystem')->disk($this->getCloudDriver()));
            });
        }

        // Interface for disk-based filesystem
        if ($this->driverExists('disk')) {
            $this->app->bind(DiskFilesystemInterface::class, function () {
                return new Filesystem($this->app->make('filesystem')->disk($this->getDiskDriver()));
            });
        }

        // If there is no default driver, we're done
        if ($this->driverExists('default') === false) {
            return;
        }

        // If default is cloud or disk, use that interface
        switch ($this->getDefaultDriver()) {
            case $this->getCloudDriver():
                $this->app->bind(FilesystemInterface::class, CloudFilesystemInterface::class);
                break;

            case $this->getDiskDriver():
                $this->app->bind(FilesystemInterface::class, DiskFilesystemInterface::class);
                break;

            default:
                // Load custom driver
                $this->app->bind(FilesystemInterface::class, function () {
                    return new Filesystem($this->app->make('filesystem')->disk($this->getDefaultDriver()));
                });
                break;
        }
    }

    /**
     * Determine if a driver is available
     *
     * @param string $driver The driver to check
     *
     * @return bool
     */
    private function driverExists(string $driver): bool
    {
        // Determine driver key from configuration
        $key = $this->app->make('config')->get(\sprintf('filesystems.%s', $driver));

        return $key !== null && $this->app->make('config')->get(\sprintf('filesystems.disks.%s', $key)) !== null;
    }

    /**
     * Get the default cloud based file driver.
     *
     * @return string|null
     */
    private function getCloudDriver(): ?string
    {
        return $this->app->make('config')->get('filesystems.cloud');
    }

    /**
     * Get the default file driver.
     *
     * @return string|null
     */
    private function getDefaultDriver(): ?string
    {
        return $this->app->make('config')->get('filesystems.default');
    }

    /**
     * Get the disk file driver.
     *
     * @return string|null
     */
    private function getDiskDriver(): ?string
    {
        return $this->app->make('config')->get('filesystems.disk');
    }

    /**
     * Register the filesystem manager
     *
     * @return void
     */
    private function registerManager(): void
    {
        $this->app->singleton('filesystem', function () {
            return new FilesystemManager($this->app);
        });
    }
}
