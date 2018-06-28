<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Utils\Exceptions\CriticalException;

class ORMException extends CriticalException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_CRITICAL;
    }

    /**
     * Display a friendly exception message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return 'A database error occurred';
    }

    /**
     * Get Error sub-code.
     *
     * @return int
     */
    public function getErrorSubCode(): int
    {
        return self::DEFAULT_ERROR_SUB_CODE;
    }
}
