<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Bridge\Laravel\Filesystem;
use EoneoPay\External\Filesystem\Exceptions\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter as ContractedFilesystem;
use League\Flysystem\Filesystem as Flysystem;
use Tests\EoneoPay\External\Bridge\Laravel\Stubs\VirtualFilesystemAdapterStub;
use Tests\EoneoPay\External\TestCase;

/**
 * @covers \EoneoPay\External\Bridge\Laravel\Filesystem
 */
class FilesystemTest extends TestCase
{
    /**
     * Test filesystem can write files to disk
     *
     * @return void
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testFilesystemCanReadAndWritesFilesToDisk(): void
    {
        $filesystem = $this->createFilesystem();

        $filename = 'test/test.txt';
        $contents = 'contents';

        self::assertTrue($filesystem->write($filename, $contents));
        self::assertTrue($filesystem->exists($filename));
        self::assertSame($contents, $filesystem->read($filename));
    }

    /**
     * Test reading a file which doesn't exists throws an exception
     *
     * @return void
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testFilesystemThrowsExceptionIfReadiningNonExistentFile(): void
    {
        $filesystem = $this->createFilesystem();

        $this->expectException(FileNotFoundException::class);

        $filesystem->read('non-existent.file');
    }

    /**
     * Create a filesystem instance for testing
     *
     * @return \EoneoPay\External\Bridge\Laravel\Filesystem
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    private function createFilesystem(): Filesystem
    {
        return new Filesystem(new ContractedFilesystem(new Flysystem(new VirtualFilesystemAdapterStub())));
    }
}
