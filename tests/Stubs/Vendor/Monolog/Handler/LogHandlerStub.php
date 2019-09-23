<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use RuntimeException;

class LogHandlerStub extends AbstractProcessingHandler
{
    /**
     * @var bool
     */
    private $isWritable;

    /**
     * @var mixed[]
     */
    private $logs = [];

    /**
     * @param bool|null $isWritable Whether the logs are writable or not
     */
    public function __construct(?bool $isWritable = null)
    {
        $this->isWritable = $isWritable ?? true;

        parent::__construct();
    }

    /**
     * Get logs.
     *
     * @return mixed[]
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param mixed[] $record
     *
     * @return void
     */
    protected function write(array $record): void
    {
        // If logs aren't writable, throw exception
        if ($this->isWritable === false) {
            throw new RuntimeException('Failed to write log file (ignore this - test only)');
        }

        $this->logs[] = $record;
    }
}
