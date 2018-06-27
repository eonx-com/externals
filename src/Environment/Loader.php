<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment;

use Dotenv\Exception\InvalidPathException as DotEnvException;
use EoneoPay\Externals\Environment\Exceptions\InvalidPathException;
use EoneoPay\Externals\Environment\Interfaces\LoaderInterface;

class Loader implements LoaderInterface
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
     * Load the env file
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Environment\Exceptions\InvalidPathException If env path is invalid
     */
    public function load(): void
    {
        // If a compiled env file exists, prefer that
        $compiled = \sprintf('%s/%s', \rtrim($this->path, '/'), \ltrim($this->compiled));
        if (\file_exists($compiled)) {
            /** @noinspection PhpIncludeInspection Dynamic file may not exist */
            /** @noinspection UsingInclusionReturnValueInspection This is how compiled files are loaded */
            $values = require $compiled;

            // If env is an array, process it otherwise fall through to .env
            if (\is_array($values)) {
                $env = new Env();

                foreach ($values as $key => $value) {
                    $env->set($key, $value);
                }

                return;
            }
        }

        // Use dot env loader and wrap path exception
        try {
            (new \Dotenv\Dotenv($this->path, $this->env))->overload();
        } catch (DotEnvException $exception) {
            throw new InvalidPathException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
