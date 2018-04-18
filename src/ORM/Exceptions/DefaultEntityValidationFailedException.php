<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Exceptions;

class DefaultEntityValidationFailedException extends EntityValidationFailedException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_VALIDATION;
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
