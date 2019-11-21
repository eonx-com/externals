<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Flysystem;

use EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Tests\EoneoPay\Externals\Stubs\Bridge\Flysystem\FlySystemStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper
 */
class FilesystemWrapperTest extends TestCase
{
    /**
     * Tests FilesystemWrapper::exists() method.
     *
     * @return void
     */
    public function testExists(): void
    {
        $filesystem = new FlySystemStub(['has' => [true]]);
        $wrapper = $this->getInstance($filesystem);

        $response = $wrapper->exists('file:///dir/foo');

        self::assertTrue($response);
        self::assertSame([['path' => 'file:///dir/foo']], $filesystem->getHasCalls());
    }

    /**
     * Test fileListing
     *
     * @return void
     */
    public function testsFileListingCalls(): void
    {
        $flysystem = new FlySystemStub(['listContents' => [['a/b/c', 'xy/z.txt']]]);
        $wrapper = $this->getInstance($flysystem);

        $actual = $wrapper->files('path/to/some/dir', true);

        self::assertSame(['a/b/c', 'xy/z.txt'], $actual);
        self::assertSame(
            [['directory' => 'path/to/some/dir', 'recursive' => true]],
            $flysystem->getListContentsCalls()
        );
    }

    /**
     * Integration test for list().
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function testFileListingBehaviour(): void
    {
        $config = new Config(['timestamp' => 1574312111]);
        $flysystem = new Filesystem(new MemoryAdapter($config), $config);
        $flysystem->write('a/b/c.txt', '123');
        $flysystem->write('a/d.txt', '456');
        $flysystem->write('x.txt', '789');
        $expected = [
            [
                'type' => 'dir',
                'timestamp' => 1574312111,
                'path' => 'a/b',
                'dirname' => 'a',
                'basename' => 'b',
                'filename' => 'b'
            ], [
                'type' => 'file',
                'visibility' => 'public',
                'timestamp' => 1574312111,
                'size' => 3,
                'path' => 'a/b/c.txt',
                'dirname' => 'a/b',
                'basename' => 'c.txt',
                'extension' => 'txt',
                'filename' => 'c'
            ], [
                'type' => 'file',
                'visibility' => 'public',
                'timestamp' => 1574312111,
                'size' => 3,
                'path' => 'a/d.txt',
                'dirname' => 'a',
                'basename' => 'd.txt',
                'extension' => 'txt',
                'filename' => 'd'
            ]
        ];

        $wrapper = $this->getInstance($flysystem);

        $actual = $wrapper->files('a', true);

        self::assertSame($expected, $actual);
    }

    /**
     * Integration test for read().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testRead(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $flysystem->write('a/b/c.txt', '123');
        $expected = '123';

        $wrapper = $this->getInstance($flysystem);

        $actual = $wrapper->read('a/b/c.txt');

        self::assertSame($expected, $actual);
    }

    /**
     * Integration test for readStream().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testReadStream(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $flysystem->write('a/b/c.txt', 'abcdefghijklmnopqrstuiwxyz');
        $expected = 'abcdefghijklmnopqrstuiwxyz';

        $wrapper = $this->getInstance($flysystem);

        $stream = $wrapper->readStream('a/b/c.txt');

        self::assertSame($expected, \stream_get_contents($stream));
    }

    /**
     * Integration test for path().
     *
     * @return void
     */
    public function testPath(): void
    {
        $flysystem = new Filesystem(new NullAdapter());
        $expected = '/a/b/c.txt';

        $wrapper = $this->getInstance($flysystem);

        $actual = $wrapper->path('a/b/c.txt');

        self::assertSame($expected, $actual);
    }

    /**
     * Integration test for write().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testWrite(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());

        $wrapper = $this->getInstance($flysystem);

        $response = $wrapper->write('x.txt', 'abc');

        self::assertTrue($response);
        self::assertSame('abc', $flysystem->read('x.txt'));
    }

    /**
     * Integration test for writeStream().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testWriteStream(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $data = 'abcdefghijklmnopqrstuiwxyz';
        $stream = fopen('php://memory','rb+');
        \fwrite($stream, $data);
        $wrapper = $this->getInstance($flysystem);

        $response = $wrapper->writeStream('st.txt', $stream);

        self::assertTrue($response);
        self::assertSame('abcdefghijklmnopqrstuiwxyz', $flysystem->read('st.txt'));
    }

    private function getInstance(
        FilesystemInterface $filesystem
    ): FilesystemWrapper
    {
        return new FilesystemWrapper($filesystem);
    }
}
