<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class GuardedStub extends EntityStub
{
    /**
     * Get entity guarded properties.
     *
     * @return string[]
     */
    protected function getGuarded(): array
    {
        return ['entityId'];
    }
}
