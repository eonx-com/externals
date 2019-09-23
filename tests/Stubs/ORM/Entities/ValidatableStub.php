<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Interfaces\ValidatableInterface;
use Tests\EoneoPay\Externals\Stubs\ORM\Exceptions\EntityValidationFailedExceptionStub;

/**
 * @ORM\Entity()
 */
class ValidatableStub extends EntityStub implements ValidatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return [
            'string' => 'required|string',
            'integer' => 'required|int',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationFailedException(): string
    {
        return EntityValidationFailedExceptionStub::class;
    }
}
