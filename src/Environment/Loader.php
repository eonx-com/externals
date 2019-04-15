<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment;

use Dotenv\Dotenv;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidPathException as DotEnvException;
use Dotenv\Loader as DotEnvLoader;
use EoneoPay\Externals\Environment\Exceptions\InvalidPathException;
use EoneoPay\Externals\Environment\Interfaces\LoaderInterface;

final class Loader implements LoaderInterface
{
    /**
     * Relative path to compiled env file
     *
     * @var string
     */
    private $compiled;

    /**
     * Relative path to env file
     *
     * @var string
     */
    private $env;

    /**
     * Path to load env from
     *
     * @var string
     */
    private $path;

    /**
     * Create a new loader instance
     *
     * @param string $path The path to load the env file from
     * @param string|null $compiled Relative path to compiled env file
     * @param string|null $env Relative path to env file
     */
    public function __construct(string $path, ?string $compiled = null, ?string $env = null)
    {
        $this->compiled = $compiled ?? 'env.php';
        $this->env = $env ?? '.env';
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function load(): void
    {
        $this->process();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function overload(): void
    {
        $this->process(true);
    }

    /**
     * Get compiled array
     *
     * @return mixed[]|null
     */
    private function getCompiled(): ?array
    {
        // If a compiled env file exists, prefer that
        $compiled = \sprintf('%s/%s', \rtrim($this->path, '/'), \ltrim($this->compiled));

        // If file doesn't exist, return
        if (\file_exists($compiled) === false) {
            return null;
        }

        /** @noinspection PhpIncludeInspection Dynamic file may not exist */
        /** @noinspection UsingInclusionReturnValueInspection This is how compiled files are loaded */
        $values = require $compiled;

        // Return values if it's an array, otherwise null
        return \is_array($values) ? $values : null;
    }

    /**
     * Process an env file
     *
     * @param bool|null $overload Whether to overwrite existing values or not
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    private function process(?bool $overload = null): void
    {
        // If a compiled env file exists, prefer that
        $compiled = $this->getCompiled();

        if (\is_array($compiled)) {
            $env = new Env();

            foreach ($compiled as $key => $value) {
                // Honour overload
                if ($overload !== true && $env->get($key) !== null) {
                    continue;
                }

                $env->set($key, $value);
            }

            return;
        }

        // Use dot env loader
        $loader = new DotEnvLoader([\sprintf('%s/%s', $this->path, $this->env)], new DotenvFactory());
        $dotenv = new Dotenv($loader);

        // Attempt to call load() or overload() and wrap any exceptions found
        try {
            $callable = [$dotenv, $overload === true ? 'overload' : 'load'];

            // Only call method if it's callable
            if (\is_callable($callable)) {
                $callable();
            }
        } catch (DotEnvException $exception) {
            throw new InvalidPathException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
