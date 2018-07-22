<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs;

use DirectoryIterator;
use FilesystemIterator;
use finfo;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\UnreadableFileException;
use League\Flysystem\Util;
use org\bovigo\vfs\vfsStream;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Class is a copy of the flysystem default
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Class is a copy of the flysystem default
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Methods are dictated by the flysystem interface
 */
class VirtualFilesystemAdapterStub extends AbstractAdapter
{
    /**
     * Default access permissions
     *
     * @var mixed[]
     */
    protected static $permissions = [
        'dir' => ['private' => 0700, 'public' => 0755],
        'file' => ['private' => 0600, 'public' => 0644]
    ];

    /**
     * Virtual file system instance
     *
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private static $vfs;

    /**
     * Create virtual file system
     *
     * @param string $root The optional root directly to use
     *
     * @throws \org\bovigo\vfs\vfsStreamException If root directory contains an invalid character
     *
     * @SuppressWarnings(PHPMD.StaticAccess) vfsStream requires methods to be statically accessed
     */
    public function __construct(?string $root = null)
    {
        self::$vfs = vfsStream::setup($root ?? 'root');

        $this->setPathPrefix(self::$vfs->url());
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath): bool
    {
        $location = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $this->ensureDirectory(\dirname($destination));

        return \copy($location, $destination);
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, Config $config)
    {
        $location = $this->applyPathPrefix($dirname);
        $return = $this->ensureDirectory($location) ? ['path' => $dirname, 'type' => 'dir'] : false;

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function delete($path): bool
    {
        $location = $this->applyPathPrefix($path);

        return \unlink($location);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname): bool
    {
        $location = $this->applyPathPrefix($dirname);

        if (\is_dir($location) === false) {
            return false;
        }

        $contents = $this->getRecursiveDirectoryIterator($location, RecursiveIteratorIterator::CHILD_FIRST);

        /** @var \SplFileInfo $file */
        foreach ($contents as $file) {
            $this->guardAgainstUnreadableFileInfo($file);
            $this->deleteFileInfoObject($file);
        }

        return \rmdir($location);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $location = $this->applyPathPrefix($path);
        $info = new SplFileInfo($location);

        return $this->normalizeFileInfo($info);
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Flysystem requires Util methods to be statically accessed
     */
    public function getMimetype($path)
    {
        $location = $this->applyPathPrefix($path);
        $finfo = new finfo(\FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($location);

        if (\in_array($mimetype, ['application/octet-stream', 'inode/x-empty'], true)) {
            /** @noinspection PhpInternalEntityUsedInspection */
            // Mimics Flysystem local driver
            $mimetype = Util\MimeType::detectByFilename($location);
        }

        return ['path' => $path, 'type' => 'file', 'mimetype' => $mimetype];
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        $location = $this->applyPathPrefix($path);
        \clearstatcache(false, $location);
        $permissions = \octdec(\substr(\sprintf('%o', \fileperms($location)), -4));
        $visibility = ($permissions & 0044) ?
            AdapterInterface::VISIBILITY_PUBLIC :
            AdapterInterface::VISIBILITY_PRIVATE;

        return \compact('path', 'visibility');
    }

    /**
     * @inheritdoc
     */
    public function has($path): bool
    {
        $location = $this->applyPathPrefix($path);

        return \file_exists($location);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = null): array
    {
        $result = [];
        $location = $this->applyPathPrefix($directory);

        if (\is_dir($location) === false) {
            return [];
        }

        $iterator = $recursive === true ?
            $this->getRecursiveDirectoryIterator($location) :
            $this->getDirectoryIterator($location);

        foreach ($iterator as $file) {
            $path = $this->getFilePath($file);

            if (\preg_match('#(^|/|\\\\)\.{1,2}$#', $path)) {
                continue;
            }

            $result[] = $this->normalizeFileInfo($file);
        }

        return \array_filter($result);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $location = $this->applyPathPrefix($path);
        $contents = \file_get_contents($location);

        if ($contents === false) {
            return false;
        }

        return ['type' => 'file', 'path' => $path, 'contents' => $contents];
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $location = $this->applyPathPrefix($path);
        $stream = \fopen($location, 'rb');

        return ['type' => 'file', 'path' => $path, 'stream' => $stream];
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Flysystem requires Util methods to be statically accessed
     */
    public function rename($path, $newpath): bool
    {
        $location = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $parentDirectory = $this->applyPathPrefix(Util::dirname($newpath));
        $this->ensureDirectory($parentDirectory);

        return \rename($location, $destination);
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        $location = $this->applyPathPrefix($path);
        $type = \is_dir($location) ? 'dir' : 'file';
        $success = \chmod($location, self::$permissions[$type][$visibility]);

        if ($success === false) {
            return false;
        }

        return \compact('path', 'visibility');
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Flysystem requires Util methods to be statically accessed
     */
    public function update($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $mimetype = Util::guessMimeType($path, $contents);
        $size = \file_put_contents($location, $contents);

        if ($size === false) {
            return false;
        }

        $type = 'file';

        return \compact('type', 'path', 'size', 'contents', 'mimetype');
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(\dirname($location));

        if (($size = \file_put_contents($location, $contents)) === false) {
            return false;
        }

        $type = 'file';
        $result = \compact('contents', 'type', 'size', 'path');

        $visibility = $config->get('visibility');
        if ($visibility !== null) {
            $result['visibility'] = $visibility;
            $this->setVisibility($path, $visibility);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(\dirname($location));
        $stream = \fopen($location, 'w+b');

        if ($stream === false) {
            return false;
        }

        \stream_copy_to_stream($resource, $stream);

        if (\fclose($stream) === false) {
            return false;
        }

        $visibility = $config->get('visibility');
        if ($visibility !== null) {
            $this->setVisibility($path, $visibility);
        }

        $type = 'file';

        return \compact('type', 'path', 'visibility');
    }

    /**
     * Delete file or directory.
     *
     * @param \SplFileInfo $file
     *
     * @return void
     */
    protected function deleteFileInfoObject(SplFileInfo $file): void
    {
        switch ($file->getType()) {
            case 'dir':
                \rmdir($file->getRealPath());
                break;

            case 'link':
                \unlink($file->getPathname());
                break;

            default:
                \unlink($file->getRealPath());
        }
    }

    /**
     * Ensure the root directory exists
     *
     * @param string $folder Folder to check
     *
     * @return bool
     *
     * @throws \RuntimeException If the directory can not be created
     */
    protected function ensureDirectory(string $folder): bool
    {
        // @see: https://github.com/kalessil/phpinspectionsea/blob/master/docs/probable-bugs.md#mkdir-race-condition
        /** @noinspection NotOptimalIfConditionsInspection */
        if (\is_dir($folder) === false &&
            \mkdir($folder, self::$permissions['dir']['public'], true) === false &&
            \is_dir($folder) === false
        ) {
            throw new RuntimeException(\sprintf('Unable to create the directory "%s".', $folder));
        }

        return true;
    }

    /**
     * Get an iterator for specified directory.
     *
     * @param string $path
     *
     * @return \DirectoryIterator
     */
    protected function getDirectoryIterator(string $path): DirectoryIterator
    {
        return new DirectoryIterator($path);
    }

    /**
     * Get the normalized path from a SplFileInfo object.
     *
     * @param \SplFileInfo $file
     *
     * @return string
     */
    protected function getFilePath(SplFileInfo $file): string
    {
        $location = $file->getPathname();
        $path = $this->removePathPrefix($location);

        return \trim(\str_replace('\\', '/', $path), '/');
    }

    /**
     * Get a recursive iterator for specified directory.
     *
     * @param string $path
     * @param null|int $mode
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getRecursiveDirectoryIterator(string $path, ?int $mode = null): RecursiveIteratorIterator
    {
        $mode = $mode ?? RecursiveIteratorIterator::SELF_FIRST;

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            $mode
        );
    }

    /**
     * Check if file or directory is readable.
     *
     * @param \SplFileInfo $file
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Flysystem requires UnreadableFileException method to be statically accessed
     */
    protected function guardAgainstUnreadableFileInfo(SplFileInfo $file): void
    {
        if ($file->isReadable() === false) {
            throw UnreadableFileException::forFileInfo($file);
        }
    }

    /**
     * Retrieve details about file or directory.
     *
     * @param \SplFileInfo $file
     *
     * @return mixed[]
     */
    protected function mapFileInfo(SplFileInfo $file): array
    {
        $normalized = [
            'type' => $file->getType(),
            'path' => $this->getFilePath($file)
        ];

        $normalized['timestamp'] = $file->getMTime();

        if ($normalized['type'] === 'file') {
            $normalized['size'] = $file->getSize();
        }

        return $normalized;
    }

    /**
     * Normalize the file info.
     *
     * @param \SplFileInfo $file
     *
     * @return mixed[]|null
     *
     * @throws \League\Flysystem\NotSupportedException
     */
    protected function normalizeFileInfo(SplFileInfo $file): ?array
    {
        return $file->isLink() === false ? $this->mapFileInfo($file) : null;
    }
}
