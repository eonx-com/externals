<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Flysystem;

use EoneoPay\Externals\Filesystem\Interfaces\FilesystemInterface;
use League\Flysystem\FilesystemInterface as FlysystemInterface;

class FilesystemWrapper implements FilesystemInterface
{
    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $flysystem;

    /**
     * FilesystemWrapper constructor.
     *
     * @param \League\Flysystem\FilesystemInterface $flysystem
     */
    public function __construct(FlysystemInterface $flysystem)
    {
        $this->flysystem = $flysystem;
    }

    public function append(string $filename, string $contents): bool
    {
        // TODO: Implement append() method.
    }

    public function exists(string $filename): bool
    {
        return $this->flysystem->has($filename);
    }

    public function files(?string $directory = null, ?bool $recursive = null): array
    {
        return $this->flysystem->listContents($directory, $recursive);
    }

    public function path(?string $filename = null): string
    {
        // TODO: Implement path() method.
    }

    public function read(string $filename): string
    {
        // TODO: Implement read() method.
    }

    public function readStream(string $filename)
    {
        // TODO: Implement readStream() method.
    }

    public function remove(string $filename): bool
    {
        // TODO: Implement remove() method.
    }

    public function write(string $filename, string $contents): bool
    {
        // TODO: Implement write() method.
    }

    public function writeStream(string $path, $resource, ?array $options = null): bool
    {
        // TODO: Implement writeStream() method.
    }
}
