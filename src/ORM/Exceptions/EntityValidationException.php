<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Exceptions;

use EoneoPay\External\ORM\Interfaces\EntityValidationExceptionInterface;
use EoneoPay\Utils\Exceptions\ValidationException;
use Throwable;

abstract class EntityValidationException extends ValidationException implements EntityValidationExceptionInterface
{
    /**
     * Validation errors.
     *
     * @var array
     */
    private $errors;

    /**
     * EntityValidationException constructor.
     *
     * @param string|null $message
     * @param int|null $code
     * @param \Throwable|null $previous
     * @param array $errors
     */
    public function __construct(string $message = null, int $code = null, Throwable $previous = null, array $errors)
    {
        parent::__construct($message ?? '', $code ?? 0, $previous);

        $this->errors = $errors;
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
