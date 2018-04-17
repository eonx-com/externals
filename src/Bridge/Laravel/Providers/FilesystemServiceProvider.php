<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Filesystem;
use EoneoPay\External\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\External\Filesystem\Interfaces\DiskFilesystemInterface;
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
        if ($this->driverExists('default')) {
            $this->app->bind(DiskFilesystemInterface::class, function () {
                return new Filesystem($this->app->make('filesystem')->disk($this->getDefaultDriver()));
            });
        }
    }

    /**
     * Get the default cloud based file driver.
     *
     * @return string|null
     */
    protected function getCloudDriver(): ?string
    {
        return $this->app->make('config')->get('filesystems.cloud');
    }

    /**
     * Get the default file driver.
     *
     * @return string|null
     */
    protected function getDefaultDriver(): ?string
    {
        return $this->app->make('config')->get('filesystems.default');
    }

    /**
     * Register the filesystem manager
     *
     * @return void
     */
    protected function registerManager(): void
    {
        $this->app->singleton('filesystem', function () {
            return new FilesystemManager($this->app);
        });
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
}
