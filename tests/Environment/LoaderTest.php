<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Environment;

use EoneoPay\Externals\Bridge\Laravel\Filesystem;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\Environment\Exceptions\InvalidPathException;
use EoneoPay\Externals\Environment\Loader;
use Illuminate\Filesystem\FilesystemAdapter as ContractedFilesystem;
use League\Flysystem\Filesystem as Flysystem;
use Tests\EoneoPay\Externals\Stubs\VirtualFilesystemAdapterStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Environment\Loader
 */
class LoaderTest extends TestCase
{
    /**
     * Test loader can read env file
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     * @throws \org\bovigo\vfs\vfsStreamException If root directory contains an invalid character
     */
    public function testLoaderCanReadEnvFile(): void
    {
        $filesystem = $this->createFilesystem();

        // Create both env files
        $filesystem->write('.env', 'TEST=env');

        // Load env file
        (new Loader($filesystem->path()))->load();

        // Test that compiled is preferred over env and the value is correct
        self::assertSame('env', (new Env())->get('TEST'));
    }

    /**
     * Test loader prefers compiled files over env files
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     * @throws \org\bovigo\vfs\vfsStreamException If root directory contains an invalid character
     */
    public function testLoaderPrefersCompiledOverEnv(): void
    {
        $filesystem = $this->createFilesystem();

        // Create both env files
        $filesystem->write('env.php', \sprintf('<?php%sreturn [\'TEST\' => \'compiled\'];', \PHP_EOL));
        $filesystem->write('.env', 'TEST=env');

        // Load env file
        (new Loader($filesystem->path()))->load();

        // Test that compiled is preferred over env and the value is correct
        self::assertSame('compiled', (new Env())->get('TEST'));
    }

    /**
     * Test loader throws exception if path is invalid
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function testLoaderThrowsExceptionIfPathInvalid(): void
    {
        $this->expectException(InvalidPathException::class);

        // Load invalid path
        (new Loader(''))->load();
    }

    /**
     * Create a file system with env files
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Filesystem
     *
     * @throws \org\bovigo\vfs\vfsStreamException If root directory contains an invalid character
     */
    private function createFilesystem(): Filesystem
    {
        return new Filesystem(new ContractedFilesystem(new Flysystem(new VirtualFilesystemAdapterStub())));
    }
}
