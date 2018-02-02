<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Exceptions;

class DefaultEntityValidationException extends EntityValidationException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return 1000;
    }

    /**
     * Get Error sub-code.
     *
     * @return int
     */
    public function getErrorSubCode(): int
    {
        return 0;
    }
}
