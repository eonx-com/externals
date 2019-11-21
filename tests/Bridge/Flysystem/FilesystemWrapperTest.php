<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Flysystem;

use EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper;
use League\Flysystem\FilesystemInterface;
use Tests\EoneoPay\Externals\Stubs\Bridge\Flysystem\FlySystemStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Flysystem\FilesystemWrapper
 */
class FilesystemWrapperTest extends TestCase
{
    /**
     * Tests FilesystemWrapper::exists() method.
     */
    public function testExists()
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
    public function testsFiles(): void
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

    private function getInstance(
        FilesystemInterface $filesystem
    ): FilesystemWrapper
    {
        return new FilesystemWrapper($filesystem);
    }
}
