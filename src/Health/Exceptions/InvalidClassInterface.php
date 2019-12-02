<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Exceptions;

use EoneoPay\Utils\Exceptions\ValidationException;

/**
 * An exception that is thrown when a health check class does not extend the expected interface.
 */
class InvalidClassInterface extends ValidationException
{
    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_VALIDATION + 300;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
