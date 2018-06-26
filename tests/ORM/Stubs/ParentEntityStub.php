<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method ArrayCollection getChildren()
 *
 * @ORM\Entity()
 */
class ParentEntityStub extends EntityStub
{
    /**
     * @ORM\OneToMany(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub", mappedBy="parent")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub", mappedBy="parentPersist")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $childrenPersist;

    /**
     * ParentEntityStub constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->children = new ArrayCollection();
        $this->childrenPersist = new ArrayCollection();
    }
}
