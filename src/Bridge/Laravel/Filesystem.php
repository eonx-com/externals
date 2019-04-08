<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException;
use EoneoPay\Externals\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\Externals\Filesystem\Interfaces\DiskFilesystemInterface;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException as ContractedFileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

final class Filesystem implements CloudFilesystemInterface, DiskFilesystemInterface
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
     * @param \Illuminate\Contracts\Filesystem\Filesystem $contract Contracted filesystem instance
     */
    public function __construct(FilesystemContract $contract)
    {
        $this->filesystem = $contract;
    }

    /**
     * @inheritdoc
     */
    public function append(string $filename, string $contents): bool
    {
        return $this->safeWrite('append', $filename, $contents);
    }

    /**
     * @inheritdoc
     */
    public function exists(string $filename): bool
    {
        return $this->filesystem->exists($filename);
    }

    /**
     * @inheritdoc
     */
    public function files(?string $directory = null, ?bool $recursive = null): array
    {
        return $this->filesystem->files($directory, $recursive ?? false);
    }

    /**
     * @inheritdoc
     */
    public function path(?string $filename = null): string
    {
        /** @noinspection PhpUndefinedMethodInspection */
        // Method is in \Illuminate\Filesystem\FilesystemAdapter
        // @see: https://github.com/illuminate/contracts/pull/6
        return $this->filesystem->path((string)$filename);
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException If file is not found
     */
    public function read(string $filename): string
    {
        try {
            return $this->filesystem->get($filename);
        } catch (ContractedFileNotFoundException $exception) {
            // Wrap exception
            throw new FileNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function remove(string $filename): bool
    {
        return $this->safeWrite('delete', $filename);
    }

    /**
     * @inheritdoc
     */
    public function write(string $filename, string $contents): bool
    {
        return $this->safeWrite('put', $filename, $contents);
    }

    /**
     * @noinspection PhpDocSignatureInspection Signature matches parameters but phpstorm doesn't understand it
     *
     * Safely perform a writable action
     *
     * @param string $action The action to perform
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return bool
     */
    private function safeWrite(string $action, ... $parameters): bool
    {
        try {
            return (bool)\call_user_func_array([$this->filesystem, $action], $parameters);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (Exception $exception) {
            // If any exception is thrown it's likely the filesystem isn't writable
            return false;
        }
    }
}
