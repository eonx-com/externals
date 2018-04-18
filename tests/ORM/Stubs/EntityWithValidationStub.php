<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Exceptions\DefaultEntityValidationFailedException;

/**
 * @ORM\Entity()
 */
class EntityWithValidationStub extends EntityStub
{
    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            'string' => 'required|string',
            'integer' => 'required|int'
        ];
    }

    /**
     * Get validation failed exception class.
     *
     * @return string
     */
    public function getValidationFailedException(): string
    {
        return DefaultEntityValidationFailedException::class;
    }
}
