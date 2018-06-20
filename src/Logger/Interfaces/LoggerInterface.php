<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger\Interfaces;

use Exception;

interface LoggerInterface
{
    /**
     * Adds a debug message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function debug(string $message, ?array $context = null): bool;

    /**
     * Adds an error message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function error(string $message, ?array $context = null): bool;

    /**
     * Record a caught exception with backtrace
     *
     * @param \Exception $exception The exception to handle
     * @param string|null $level The log level for this exception
     *
     * @return bool
     */
    public function exception(Exception $exception, ?string $level = null): bool;

    /**
     * Adds an informational message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function info(string $message, ?array $context = null): bool;

    /**
     * Adds a notice to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function notice(string $message, ?array $context = null): bool;

    /**
     * Adds a warning to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function warning(string $message, ?array $context = null): bool;
}
