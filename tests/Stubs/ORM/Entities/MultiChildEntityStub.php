<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @method ArrayCollection getParents()
 *
 * @ORM\Entity()
 */
class MultiChildEntityStub extends Entity
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
     * @ORM\ManyToMany(
     *     inversedBy="children",
     *     targetEntity="Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentEntityStub"
     * )
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $parents;

    /**
     * @ORM\Column(name="value", type="string")
     *
     * @var string
     */
    protected $value;

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
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function addParent(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent, 'children');
    }

    /**
     * Add parent with no association.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function addParentWithNoAssociation(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent);
    }

    /**
     * Add parent with wrong association for test purposes.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function addParentWithWrongAssociation(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent, 'invalid');
    }

    /**
     * Add parent with wrong attribute for test purposes.
     *
     * @param \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function addParentWithWrongAttribute(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('invalid', $parent, 'children');
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

    /**
     * @inheritdoc
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }
}
