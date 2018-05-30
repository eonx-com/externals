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
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function setInvalidParent(ParentEntityStub $parent): self
    {
        return $this->associate('parent', $parent, 'invalid');
    }

    /**
     *
     * Set parent.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache isn't caching annotations
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute doesn't exist on entity
     * @throws \ReflectionException Inherited, if class or property does not exist
     */
    public function setParent(ParentEntityStub $parent): self
    {
        return $this->associate('parent', $parent, 'children');
    }
}
