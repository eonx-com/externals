<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @method ArrayCollection getChildren()
 *
 * @ORM\Entity()
 */
class MultiParentStub extends Entity
{
    /**
     * @ORM\ManyToMany(
     *     mappedBy="parents",
     *     targetEntity="MultiChildStub"
     * )
     *
     * phpcs:disable
     * @phpstan-var \Doctrine\Common\Collections\Collection<int, \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildStub>
     * phpcs:enable
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $children;

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $entityId;

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
     * Serialize entity as an array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }
}
