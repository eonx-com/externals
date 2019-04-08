<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method null|ParentEntityStub getParent()
 *
 * @ORM\Entity()
 */
class ChildEntityStub extends EntityStub
{
    /**
     * @ORM\ManyToOne(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub", inversedBy="children")
     *
     * @var \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub
     */
    protected $parent;

    /**
     * @ORM\Column(name="parent_id", type="guid")
     *
     * @var string
     */
    protected $parentId;

    /**
     * @ORM\ManyToOne(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub", inversedBy="childrenPersist")
     *
     * @var \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub
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

        $this->parent = new ParentEntityStub();
        $this->parent->getChildren()->add($this);
    }

    /**
     * Return an array of annotation/attribute pairs to search for properties in
     *
     * Note: Changing this array will cause the test testPropertyAnnotationsContainsInvalidClassAndAttribute() to fail
     *
     * @return mixed[]
     *
     * @see \Tests\EoneoPay\Externals\ORM\EntityTest::testPropertyAnnotationsContainsInvalidClassAndAttribute
     */
    public function getPropertyAnnotations(): array
    {
        parent::getPropertyAnnotations();

        return [];
    }

    /**
     * Set parent with invalid relation.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute doesn't exist on entity
     */
    public function setInvalidParent(ParentEntityStub $parent): self
    {
        return $this->associate('parent', $parent, 'invalid');
    }

    /**
     * Set parent.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub|null $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute doesn't exist on entity
     */
    public function setParent(?ParentEntityStub $parent = null): self
    {
        return $this->associate('parent', $parent, 'children');
    }

    /**
     * Set parent persist.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute doesn't exist on entity
     */
    public function setParentPersist(ParentEntityStub $parent): self
    {
        return $this->associate('parentPersist', $parent, 'childrenPersist');
    }
}
