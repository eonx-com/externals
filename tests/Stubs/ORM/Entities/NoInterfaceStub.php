<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class NoInterfaceStub
{
    /**
     * Primary id.
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;
}
