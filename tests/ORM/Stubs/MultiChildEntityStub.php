<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method ArrayCollection getParents()
 */
class MultiChildEntityStub extends EntityStub
{
    /**
     * @ORM\ManyToMany(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub", mappedBy="children")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $parents;

    /**
     * ParentEntityStub constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->parents = new ArrayCollection();
    }

    /**
     * Add parent.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function addParent(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent, 'children');
    }
}
