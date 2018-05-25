<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Environment;

use Closure;
use Dotenv\Loader;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;

class Env implements EnvInterface
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
        $this->dotenv = new Loader('');
    }

    /**
     * Gets the value of an environment variable
     *
     * @param string $key The key to get
     * @param mixed $default The value to return if the key isn't set
     *
     * @return mixed
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
     * Remove an environment variable
     *
     * @param string $key The key to remove
     *
     * @return bool
     */
    public function remove(string $key): bool
    {
        // Set env with no value to unset
        $this->dotenv->clearEnvironmentVariable($key);

        return true;
    }

    /**
     * Set an environment variable
     *
     * @param string $key The key to set
     * @param mixed $value The value to assign to the key
     *
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        // If value isn't scalar or null return failure
        if (\is_scalar($value) === false && $value !== null) {
            return false;
        }

        // Set in env
        $this->dotenv->setEnvironmentVariable($key, $value);

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
        // Handle php keywords
        switch (\mb_strtolower($value)) {
            case 'false':
            case '(false)':
                return false;

            case 'true':
            case '(true)':
                return true;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}
