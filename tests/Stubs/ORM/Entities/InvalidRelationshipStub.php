<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method null|ParentStub getParent()
 *
 * @ORM\Entity()
 */
class InvalidRelationshipStub extends EntityStub
{
    /**
     * @ORM\ManyToOne(targetEntity="ParentStub")
     *
     * @var \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub
     */
    protected $parent;

    /**
     * @ORM\Column(name="parent_id", type="guid")
     *
     * @var string
     */
    protected $parentId;

    /**
     * Set parent.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\InvalidRelationshipStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If attribute doesn't exist on entity
     */
    public function setParent(ParentStub $parent): self
    {
        return $this->associate('invalid', $parent);
    }
}
