<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger\Interfaces;

use Exception;
use Psr\Log\LoggerInterface as BaseInterface;

interface LoggerInterface extends BaseInterface
{
    /**
     * Record a caught exception with backtrace
     *
     * @param \Exception $exception The exception to handle
     * @param string|null $level The log level for this exception
     *
     * @return void
     */
    public function exception(Exception $exception, ?string $level = null): void;
}
