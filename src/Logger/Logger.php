<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Exception;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger as MonologLogger;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Methods are dictated by PSR logger interface
 */
final class Logger implements LoggerInterface
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
     * @param \Monolog\Processor\ProcessorInterface[]|null $processors
     */
    public function __construct(
        ?string $streamName = null,
        ?HandlerInterface $handler = null,
        ?array $processors = null
    ) {
        // Instantiate logger and set handler
        $this->monolog = new MonologLogger($streamName ?? 'Application');
        $this->monolog->pushHandler($handler ?? new SyslogHandler('ErrorLog'));

        foreach ($processors ?? [] as $processor) {
            $this->monolog->pushProcessor($processor);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, ?array $context = null): void
    {
        $this->log('alert', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, ?array $context = null): void
    {
        $this->log('critical', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, ?array $context = null): void
    {
        $this->log('debug', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, ?array $context = null): void
    {
        $this->log('emergency', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, ?array $context = null): void
    {
        $this->log('error', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function exception(Exception $exception, ?string $level = null): void
    {
        $this->log(
            $level ?? 'notice',
            \sprintf('Exception caught: %s', $exception->getMessage()),
            $exception->getTrace()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, ?array $context = null): void
    {
        $this->log('info', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, ?array $context = null): void
    {
        try {
            $callable = [$this->monolog, $level];

            if (\is_callable($callable) === true) {
                $callable($message, $context ?? []);
            }
        } catch (Exception $exception) {
            /** @noinspection ForgottenDebugOutputInspection This is only a fallback if logger is unavailable */
            \error_log($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, ?array $context = null): void
    {
        $this->log('notice', $message, $context ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, ?array $context = null): void
    {
        $this->log('warning', $message, $context ?? []);
    }
}
