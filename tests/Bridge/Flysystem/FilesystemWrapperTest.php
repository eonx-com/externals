<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Flysystem;

use EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper;
use EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Lots of tests are good.
 */
class FilesystemWrapperTest extends TestCase
{
    /**
     * Integration test for append().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testAppend(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $flysystem->write('half.txt', 'abcdefghijk');
        $data = 'lmnopqrstuiwxyz';
        $wrapper = $this->getInstance($flysystem);

        $response = $wrapper->append('half.txt', $data);

        self::assertTrue($response);
        self::assertSame('abcdefghijklmnopqrstuiwxyz', $flysystem->read('half.txt'));
    }

    /**
     * Tests FilesystemWrapper::exists() method.
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function testExists(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $flysystem->write('a.txt', '123');
        $wrapper = $this->getInstance($flysystem);

        $existsResponse = $wrapper->exists('a.txt');
        $notExistsResponse = $wrapper->exists('b.txt');

        self::assertTrue($existsResponse);
        self::assertFalse($notExistsResponse);
    }

    /**
     * Integration test for list().
     *
     * @return void
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
     * Catch errors for missing files.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException
     */
    public function testReadError(): void
    {
        $flysystem = new Filesystem(new MemoryAdapter());
        $wrapper = $this->getInstance($flysystem);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found at path: a/b/c.txt');

        $wrapper->read('a/b/c.txt');
    }

    /**
     * Catch errors for missing files.
     *
     * This is for errors when the underlying adapter returns false.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException
     */
    public function testReadErrorFalse(): void
    {
        $flysystem = new Filesystem(new NullAdapter(), new Config(['disable_asserts' => true]));
        $wrapper = $this->getInstance($flysystem);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found at path: x.txt');

        $wrapper->read('x.txt');
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

        /** @var resource $stream */
        $stream = $wrapper->readStream('a/b/c.txt');

        self::assertSame($expected, \stream_get_contents($stream));
    }

    /**
     * Integration test for readStream().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testReadStreamFailure(): void
    {
        $flysystem = new Filesystem(new NullAdapter(), new Config(['disable_asserts' => true]));

        $wrapper = $this->getInstance($flysystem);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found at path: null.txt');

        $wrapper->readStream('null.txt');
    }

    /**
     * Integration test for path().
     *
     * @return void
     */
    public function testPath(): void
    {
        $flysystem = new Filesystem(new NullAdapter());
        $expected = 'a/b/c.txt';

        $wrapper = $this->getInstance($flysystem);

        $actual = $wrapper->path('a/b/c.txt');

        self::assertSame($expected, $actual);
    }

    /**
     * Integration test for remove().
     *
     * @return void
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testRemove(): void
    {
        $config = new Config(['timestamp' => 1574312111]);
        $flysystem = new Filesystem(new MemoryAdapter($config), $config);
        $flysystem->write('a/b/c.txt', '123');
        $flysystem->write('x.txt', '789');
        $expected = [
            [
                'type' => 'dir',
                'timestamp' => 1574312111,
                'path' => 'a',
                'dirname' => '',
                'basename' => 'a',
                'filename' => 'a'
            ],
            [
                'type' => 'file',
                'visibility' => 'public',
                'timestamp' => 1574312111,
                'size' => 3,
                'path' => 'x.txt',
                'dirname' => '',
                'basename' => 'x.txt',
                'extension' => 'txt',
                'filename' => 'x'
            ]
        ];

        $wrapper = $this->getInstance($flysystem);

        $response = $wrapper->remove('a/b/c.txt');

        self::assertTrue($response);
        self::assertSame($expected, $flysystem->listContents('/'));
    }
    /**
     * Integration test for remove() when run against directories.
     *
     * @return void
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testRemoveDirectories(): void
    {
        // Localfile system is required, as directories are not fully supported in MemorySystem.
        $config = new Config(['timestamp' => 1574312111]);
        $flysystem = new Filesystem(new MemoryAdapter($config), $config);
        $flysystem->put('test/b/c.txt', '123');
        $flysystem->put('test/b.txt', 'abc');
        $flysystem->createDir('test/d/e/f/g');
        $expected = [
            [
                'type' => 'file',
                'visibility' => 'public',
                'timestamp' => 1574312111,
                'size' => 3,
                'path' => 'test/b.txt',
                'dirname' => 'test',
                'basename' => 'b.txt',
                'extension' => 'txt',
                'filename' => 'b'
            ],[
                'type' => 'dir',
                'timestamp' => 1574312111,
                'path' => 'test/d',
                'dirname' => 'test',
                'basename' => 'd',
                'filename' => 'd'
            ]
        ];

        $wrapper = $this->getInstance($flysystem);

        $response = $wrapper->remove('test/b');

        self::assertTrue($response);
        self::assertSame($expected, $flysystem->listContents('test'));
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
        /** @var resource $stream */
        $stream = \fopen('php://memory', 'rb+');
        \fwrite($stream, $data);
        $wrapper = $this->getInstance($flysystem);

        /** @var resource $stream */
        $response = $wrapper->writeStream('st.txt', $stream);

        self::assertTrue($response);
        self::assertSame('abcdefghijklmnopqrstuiwxyz', $flysystem->read('st.txt'));
    }

    /**
     * Create a wrapper around Flysystem.
     *
     * @param \League\Flysystem\FilesystemInterface $filesystem
     *
     * @return \EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper
     */
    private function getInstance(
        FilesystemInterface $filesystem
    ): FilesystemWrapper {
        return new FilesystemWrapper($filesystem);
    }
}
