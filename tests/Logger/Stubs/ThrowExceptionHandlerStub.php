<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger\Stubs;

use Monolog\Handler\AbstractProcessingHandler;

class ThrowExceptionHandlerStub extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     *
     * @throws \RuntimeException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Throw exception directly for test purposes
     */
    protected function write(array $record): void
    {
        throw new \RuntimeException('Just for test purposes');
    }
}
