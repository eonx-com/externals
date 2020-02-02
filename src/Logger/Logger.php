<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\AbstractLogger;
use Throwable;

final class Logger extends AbstractLogger implements LoggerInterface
{
    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Set up log writer.
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

        // In exceptional circumstances, try a last ditch effort to log to error_log.
        $this->monolog->setExceptionHandler(static function (Throwable $exception): void {
            /** @noinspection ForgottenDebugOutputInspection */
            \error_log(\sprintf(
                'An error occurred trying to write log messages. (%s - %s)',
                \get_class($exception),
                $exception->getMessage()
            ));

            throw $exception;
        });

        foreach ($processors ?? [] as $processor) {
            $this->monolog->pushProcessor($processor);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exception(Throwable $exception, ?string $level = null, ?array $context = null): void
    {
        $this->log(
            $level ?? 'notice',
            \sprintf('Exception caught: %s', $exception->getMessage()),
            \array_merge(
                $context ?? [],
                ['trace' => $exception->getTrace()]
            )
        );
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param mixed[] $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     *
     * phpcs:disable
     * Unable to add string typehint to $message
     */
    public function log($level, $message, ?array $context = null): void
    {
        // phpcs:enable
        $this->monolog->log($level, $message, $context ?? []);
    }

    /**
     * Adds a handler to the monolog stack.
     *
     * @param \Monolog\Handler\HandlerInterface $handler
     *
     * @return void
     */
    public function pushHandler(HandlerInterface $handler): void
    {
        $this->monolog->pushHandler($handler);
    }
}
