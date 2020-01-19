<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Logger;
use Exception;
use Monolog\Processor\ProcessorInterface;
use RuntimeException;
use Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler\LogHandlerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Logger\Logger
 *
 * @SuppressWarnings(PHPMD) Unable to suppress unused formal parameter for __invoke.
 */
class LoggerTest extends TestCase
{
    /**
     * Test logger when an exception occurs inside monolog.
     *
     * @return void
     */
    public function testErrorLogFallback(): void
    {
        $handler = new LogHandlerStub();
        $processor = new class() implements ProcessorInterface {
            /**
             * Processes.
             *
             * phpcs:disable
             *
             * @param mixed[] $record
             *
             * @return mixed[]
             *
             * phpcs:enable
             */
            public function __invoke(array $record): array
            {
                throw new RuntimeException('Error inside monolog - This is expected.');
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error inside monolog - This is expected.');

        $logger = new Logger(null, $handler, [$processor]);
        $logger->warning('Message');
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
             * Processes.
             *
             * @param mixed[] $record
             *
             * @return mixed[]
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
     * Tests that pushHandler adds a handler to monolog.
     *
     * @return void
     */
    public function testPushHandler(): void
    {
        $logger = new Logger(null, new LogHandlerStub());

        $handler = new LogHandlerStub();
        $logger->pushHandler($handler);
        $logger->warning('Message');

        $logs = $handler->getLogs();

        self::assertCount(1, $logs);
        self::assertSame('Message', $logs[0]['message'] ?? null);
    }
}
