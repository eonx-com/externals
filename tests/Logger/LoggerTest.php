<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Logger;
use Exception;
use Monolog\Processor\ProcessorInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler\LogHandlerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Logger\Logger
 */
class LoggerTest extends TestCase
{
    /**
     * Test logger create right logs for all bool methods.
     *
     * @return void
     */
    public function testBooleanMethods(): void
    {
        $handler = new LogHandlerStub();
        $logger = new Logger(null, $handler);
        $message = 'my message';
        $context = ['attr' => 'value'];

        foreach (['alert', 'critical', 'debug', 'emergency', 'error', 'info', 'notice', 'warning'] as $method) {
            $callable = [$logger, $method];

            if (\is_callable($callable) === false) {
                self::fail(\sprintf('Unable to call %s on %s', $method, Logger::class));

                continue;
            }

            $callable($message, $context);
        }

        foreach ($handler->getLogs() as $log) {
            self::assertArrayHasKey('message', $log);
            self::assertArrayHasKey('context', $log);
            self::assertEquals($message, $log['message']);
            self::assertEquals($context, $log['context']);
        }

        $this->addToAssertionCount(1);
    }

    /**
     * Test logger create right log for exception.
     *
     * @return void
     */
    public function testLogException(): void
    {
        $handler = new LogHandlerStub();
        $logger = new Logger(null, $handler);
        $message = 'my message';

        $logger->exception(new Exception($message), 'warning', [
            'extra' => 'stuff',
        ]);
        $logs = $handler->getLogs();

        self::assertCount(1, $logs);

        $log = \reset($logs);

        self::assertArrayHasKey('message', $log);
        self::assertSame(\sprintf('Exception caught: %s', $message), $log['message']);
        self::assertSame(300, $log['level']);
        self::assertSame('stuff', $log['context']['extra']);
    }

    /**
     * Test logger processors.
     *
     * @return void
     */
    public function testLogProcessors(): void
    {
        $handler = new LogHandlerStub();
        $processor = new class() implements ProcessorInterface {
            /**
             * {@inheritdoc}
             */
            public function __invoke(array $record): array
            {
                $record['extra']['details'] = 'Details';

                return $record;
            }
        };

        $logger = new Logger(null, $handler, [$processor]);
        $logger->warning('Message');

        $logs = $handler->getLogs();

        self::assertCount(1, $logs);

        $log = \reset($logs);

        self::assertArrayHasKey('extra', $log);
        self::assertArrayHasKey('details', $log['extra']);
        self::assertSame('Details', $log['extra']['details']);
    }

    /**
     * Test logger handles Monolog exceptions.
     *
     * @return void
     */
    public function testLoggerContinuesWhenMonologExceptionThrown(): void
    {
        (new Logger(null, new LogHandlerStub(false)))->error('message');

        $this->addToAssertionCount(1);
    }
}
