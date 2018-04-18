<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger\Stubs;

use Monolog\Handler\AbstractProcessingHandler;

class ArrayHandlerStub extends AbstractProcessingHandler
{
    /**
     * @var array
     */
    private $logs = [];

    /**
     * Get logs.
     *
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record): void
    {
        $this->logs[] = $record;
    }
}
