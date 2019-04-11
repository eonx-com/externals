<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment;

use Closure;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Loader;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;

final class Env implements EnvInterface
{
    /**
     * Dotenv loader instance
     *
     * @var \Dotenv\Loader
     */
    private $dotenv;

    /**
     * Create a new env instance
     */
    public function __construct()
    {
        $this->dotenv = new Loader([], new DotenvFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        $value = $this->dotenv->getEnvironmentVariable($key);

        // If value doesn't exist, return default
        if ($value === null) {
            return $default instanceof Closure ? $default() : $default;
        }

        // If value includes quotes return substring
        if (\mb_strlen($value) > 1 && \mb_strpos($value, '"') === 0 && \mb_substr($value, -1) === '"') {
            $value = \mb_substr($value, 1, -1);
        }

        return $this->resolveKeywords($value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        // Set env with no value to unset
        $this->dotenv->clearEnvironmentVariable($key);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): bool
    {
        // If value isn't scalar or null return failure
        if (\is_scalar($value) === false && $value !== null) {
            return false;
        }

        // Set in env
        $this->dotenv->setEnvironmentVariable($key, (string)$value);

        return true;
    }

    /**
     * Process a string for keywords and return keyword value if found
     *
     * @param string $value The value to process
     *
     * @return mixed
     */
    private function resolveKeywords(string $value)
    {
        // By default the original value will be returned
        $resolution = $value;

        // Handle php keywords - code coverage disabled due to phpdbg not seeing case statements
        switch (\mb_strtolower($value)) {
            case 'false': // @codeCoverageIgnore
            case '(false)': // @codeCoverageIgnore
                $resolution = false;
                break;

            case 'true': // @codeCoverageIgnore
            case '(true)': // @codeCoverageIgnore
                $resolution = true;
                break;

            case 'empty': // @codeCoverageIgnore
            case '(empty)': // @codeCoverageIgnore
                $resolution = '';
                break;

            case 'null': // @codeCoverageIgnore
            case '(null)': // @codeCoverageIgnore
                $resolution = null;
                break;
        }

        return $resolution;
    }
}
