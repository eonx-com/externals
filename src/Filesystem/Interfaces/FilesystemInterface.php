<?php
declare(strict_types=1);

namespace EoneoPay\External\Filesystem\Interfaces;

interface FilesystemInterface
{
    /**
     * Check whether a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get contents of a file
     *
     * @param string $path The path to the file
     *
     * @return string
     *
     * @throws \EoneoPay\External\Filesystem\Exceptions\FileNotFoundException If file is not found
     */
    public function read(string $path): string;

    /**
     * Write a file to the filesystem
     *
     * @param string $path The path to write to
     * @param string $contents The contents to write to the file
     *
     * @return bool
     */
    public function write(string $path, string $contents): bool;
}
