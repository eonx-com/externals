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
        $existing = $this->readStream($filename);

        $writeStream = \fopen('php://memory','ab+');
        \stream_copy_to_stream($existing, $writeStream);
        \fwrite($writeStream, $contents);

        return $this->flysystem->updateStream($filename, $writeStream);
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
        return '/' . $filename;
    }

    public function read(string $filename): string
    {
        return $this->flysystem->read($filename);
    }

    public function readStream(string $filename)
    {
        return $this->flysystem->readStream($filename);
    }

    public function remove(string $filename): bool
    {
        return $this->flysystem->delete($filename);
    }

    public function write(string $filename, string $contents): bool
    {
        return $this->flysystem->write($filename, $contents);
    }

    public function writeStream(string $path, $resource, ?array $options = null): bool
    {
        return $this->flysystem->writeStream($path, $resource);
    }
}
