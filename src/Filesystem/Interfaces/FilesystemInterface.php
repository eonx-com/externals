<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Filesystem\Interfaces;

interface FilesystemInterface
{
    /**
     * Check whether a file exists
     *
     * @param string $filename The file to check
     *
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * Get the full path to a file
     *
     * @param string|null $filename The filename to append to the path
     *
     * @return string
     */
    public function path(?string $filename = null): string;

    /**
     * Get contents of a file
     *
     * @param string $filename The filename to read from
     *
     * @return string
     *
     * @throws \EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException If file is not found
     */
    public function read(string $filename): string;

    /**
     * Remove a file from the filesystem
     *
     * @param string $filename The filename to remove
     *
     * @return bool
     */
    public function remove(string $filename): bool;

    /**
     * Write a file to the filesystem
     *
     * @param string $filename The filename to write to
     * @param string $contents The contents to write to the file
     *
     * @return bool
     */
    public function write(string $filename, string $contents): bool;
}
