<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method ArrayCollection getChildren()
 */
class MultiParentEntityStub extends EntityStub
{
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
}
