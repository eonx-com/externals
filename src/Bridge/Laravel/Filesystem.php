<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Filesystem\Exceptions\FileNotFoundException;
use EoneoPay\External\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\External\Filesystem\Interfaces\DiskFilesystemInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException as ContractedFileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class Filesystem implements CloudFilesystemInterface, DiskFilesystemInterface
{
    /**
     * Contracted filesystem instance
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Create new filesystem instance
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $contract Contracted filesystem isntance
     */
    public function __construct(FilesystemContract $contract)
    {
        $this->filesystem = $contract;
    }

    /**
     * Check whether a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * Get contents of a file
     *
     * @param string $path The path to the file
     *
     * @return string
     *
     * @throws \EoneoPay\External\Filesystem\Exceptions\FileNotFoundException If file is not found
     */
    public function read(string $path): string
    {
        try {
            return $this->filesystem->get($path);
        } catch (ContractedFileNotFoundException $exception) {
            // Wrap exception
            throw new FileNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Write a file to the filesystem
     *
     * @param string $path The path to write to
     * @param string $contents The contents to write to the file
     *
     * @return bool
     */
    public function write(string $path, string $contents): bool
    {
        return $this->filesystem->put($path, $contents);
    }
}
