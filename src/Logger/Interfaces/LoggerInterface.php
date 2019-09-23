<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger\Interfaces;

use Psr\Log\LoggerInterface as BaseInterface;
use Throwable;

interface LoggerInterface extends BaseInterface
{
    /**
     * Record a caught exception with backtrace.
     *
     * @param \Throwable $exception The exception to handle
     * @param string|null $level The log level for this exception
     * @param mixed[]|null $context
     *
     * @return void
     */
    public function exception(Throwable $exception, ?string $level = null, ?array $context = null): void;
}
