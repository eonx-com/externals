<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Exceptions;

use EoneoPay\External\ORM\Interfaces\EntityValidationFailedExceptionInterface;
use EoneoPay\Utils\Exceptions\ValidationException;

abstract class EntityValidationFailedException extends ValidationException implements EntityValidationFailedExceptionInterface
{
    //
}
