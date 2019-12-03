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
     * Test loader can read env file.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testLoaderCanReadEnvFile(): void
    {
        $filesystem = $this->createFilesystem();

        // Clear env value
        $env = new Env();
        $env->remove('TEST');

        // Create env file
        $filesystem->write('.env', 'TEST=env');

        // Load env file
        (new Loader($filesystem->path()))->load();

        // Test that compiled is preferred over env and the value is correct
        self::assertSame('env', $env->get('TEST'));
    }

    /**
     * Test load doesn't overwrite existing values but overload does.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testLoaderOverloadsOnlyWhenRequested(): void
    {
        $filesystem = $this->createFilesystem();

        // Create a value
        $env = new Env();
        $env->set('TEST', 'exists');

        // Create env file
        $filesystem->write('env.php', \sprintf('<?php%sreturn [\'TEST\' => \'compiled\'];', \PHP_EOL));

        // Load env file
        $loader = new Loader($filesystem->path());
        $loader->load();

        // Test that value hasn't changed
        self::assertSame('exists', $env->get('TEST'));

        // Load with overload
        $loader->overload();

        // Test value has changed
        self::assertSame('compiled', $env->get('TEST'));
    }

    /**
     * Test loader prefers compiled files over env files.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testLoaderPrefersCompiledOverEnv(): void
    {
        $filesystem = $this->createFilesystem();

        // Clear env value
        $env = new Env();
        $env->remove('TEST');

        // Create both env files
        $filesystem->write('env.php', \sprintf('<?php%sreturn [\'TEST\' => \'compiled\'];', \PHP_EOL));
        $filesystem->write('.env', 'TEST=env');

        // Load env file
        (new Loader($filesystem->path()))->load();

        // Test that compiled is preferred over env and the value is correct
        self::assertSame('compiled', $env->get('TEST'));
    }

    /**
     * Test loader throws exception if path is invalid.
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
     * Create a file system with env files.
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Filesystem
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    private function createFilesystem(): Filesystem
    {
        return new Filesystem(new ContractedFilesystem(new Flysystem(new VirtualFilesystemAdapterStub())));
    }
}
