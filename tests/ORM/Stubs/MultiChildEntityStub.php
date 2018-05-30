<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

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
     * @ORM\Column(name="value", type="string")
     *
     * @var string
     */
    protected $value;

    /**
     * @ORM\ManyToMany(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub", inversedBy="children")
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
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function addParent(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent, 'children');
    }

    /**
     * Add parent with no association.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function addParentWithNoAssociation(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent);
    }

    /**
     * Add parent with wrong attribute for test purposes.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function addParentWithWrongAttribute(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('invalid', $parent, 'children');
    }

    /**
     * Add parent with wrong association for test purposes.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function addParentWithWrongAssociation(MultiParentEntityStub $parent): self
    {
        return $this->associateMultiple('parents', $parent, 'invalid');
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
