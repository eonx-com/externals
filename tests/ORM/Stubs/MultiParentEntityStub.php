<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @method ArrayCollection getChildren()
 *
 * @ORM\Entity()
 */
class MultiParentEntityStub extends Entity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $entityId;

    /**
     * @ORM\ManyToMany(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub", mappedBy="parents")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $children;

    /**
     * ParentEntityStub constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->children = new ArrayCollection();
    }

    /**
     * Serialize entity as an array
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [];
    }
}
