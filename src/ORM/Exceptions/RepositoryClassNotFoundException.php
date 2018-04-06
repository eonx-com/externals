<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;

class RepositoryClassNotFoundException extends RuntimeException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_STATUS_CODE_RUNTIME;
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