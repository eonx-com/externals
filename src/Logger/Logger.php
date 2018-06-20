<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Exception;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger as MonologLogger;

class Logger implements LoggerInterface
{
    /**
     * Monolog instance
     *
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Set up log writer
     *
     * @param string|null $streamName The log stream name
     * @param \Monolog\Handler\HandlerInterface|null $handler The handler to use for logging
     */
    public function __construct(?string $streamName = null, ?HandlerInterface $handler = null)
    {
        // Instantiate logger and set handler
        $this->monolog = new MonologLogger($streamName ?? 'Application');
        $this->monolog->pushHandler($handler ?? new SyslogHandler('ErrorLog'));
    }

    /**
     * Adds a debug message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function debug(string $message, ?array $context = null): bool
    {
        return $this->write('debug', $message, $context ?? []);
    }

    /**
     * Adds an error message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function error(string $message, ?array $context = null): bool
    {
        return $this->write('error', $message, $context ?? []);
    }

    /**
     * Record a caught exception with backtrace
     *
     * @param \Exception $exception The exception to handle
     * @param string|null $level The log level for this exception
     *
     * @return bool
     */
    public function exception(Exception $exception, ?string $level = null): bool
    {
        return $this->write(
            $level ?? 'notice',
            \sprintf('Exception caught: %s', $exception->getMessage()),
            $exception->getTrace()
        );
    }

    /**
     * Adds an informational message to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function info(string $message, ?array $context = null): bool
    {
        return $this->write('info', $message, $context ?? []);
    }

    /**
     * Adds a notice to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function notice(string $message, ?array $context = null): bool
    {
        return $this->write('notice', $message, $context ?? []);
    }

    /**
     * Adds a warning to the log
     *
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    public function warning(string $message, ?array $context = null): bool
    {
        return $this->write('warning', $message, $context ?? []);
    }

    /**
     * Write a log and return the result
     *
     * @param string $type The log type
     * @param string $message The log message
     * @param mixed[] $context Additional log context
     *
     * @return bool
     */
    private function write(string $type, string $message, ?array $context = null): bool
    {
        try {
            return $this->monolog->{$type}($message, $context ?? []);
        } catch (Exception $exception) {
            // Logger is unavailable, write to php error log
            /** @noinspection ForgottenDebugOutputInspection */
            \error_log($exception->getMessage());
        }

        // Log wasn't written
        return false;
    }
}
