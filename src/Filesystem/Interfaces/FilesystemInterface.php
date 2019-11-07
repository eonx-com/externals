<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Filesystem\Interfaces;

interface FilesystemInterface
{
    /**
     * Append to a file
     *
     * @param string $filename The filename to write to
     * @param string $contents The contents to write to the file
     *
     * @return bool
     */
    public function append(string $filename, string $contents): bool;

    /**
     * Check whether a file exists
     *
     * @param string $filename The file to check
     *
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * Get an array of all files in a directory.
     *
     * @param string|null $directory The directory to retrieve the files from
     * @param bool|null $recursive Either to retrieve files from sub-directories
     *
     * @return mixed[]
     */
    public function files(?string $directory = null, ?bool $recursive = null): array;

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
     */
    public function read(string $filename): string;

    /**
     * Get a resource to read the file.
     *
     * @param string $filename The filename to read from
     *
     * @return null|resource
     */
    public function readStream(string $filename);

    /**
     * Remove a file from the filesystem.
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

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param null|mixed[] $options
     *
     * @return bool
     */
    public function writeStream(string $path, $resource, ?array $options = null): bool;
}
