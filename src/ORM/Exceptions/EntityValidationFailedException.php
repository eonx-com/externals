<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Exceptions;

use EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface;
use EoneoPay\Utils\Exceptions\ValidationException;

abstract class EntityValidationFailedException extends ValidationException implements
    EntityValidationFailedExceptionInterface
{
}
