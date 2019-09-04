<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method null|ParentStub getParent()
 *
 * @ORM\Entity()
 */
class ChildStub extends EntityStub
{
    /**
     * @ORM\ManyToOne(
     *     inversedBy="children",
     *     targetEntity="ParentStub"
     * )
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
     * @ORM\ManyToOne(
     *     inversedBy="childrenPersist",
     *     targetEntity="ParentStub"
     * )
     *
     * @var \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub
     */
    protected $parentPersist;

    /**
     * ChildEntityStub constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->parent = new ParentStub();
        $this->parent->getChildren()->add($this);
    }

    /**
     * Set parent with invalid relation.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ChildStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If attribute doesn't exist on entity
     */
    public function setInvalidParent(ParentStub $parent): self
    {
        return $this->associate('parent', $parent, 'invalid');
    }

    /**
     * Set parent.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub|null $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ChildStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If attribute doesn't exist on entity
     */
    public function setParent(?ParentStub $parent): self
    {
        return $this->associate('parent', $parent, 'children');
    }

    /**
     * Set parent persist.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\ChildStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If attribute doesn't exist on entity
     */
    public function setParentPersist(ParentStub $parent): self
    {
        return $this->associate('parentPersist', $parent, 'childrenPersist');
    }
}
