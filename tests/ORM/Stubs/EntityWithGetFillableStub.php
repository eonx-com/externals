<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class EntityWithGetFillableStub extends EntityStub
{
    /**
     * Get entity fillable properties.
     *
     * @return string[]
     */
    protected function getFillable(): array
    {
        return ['entityId', 'integer', 'string'];
    }
}
