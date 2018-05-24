<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @ORM\Entity()
 */
class EntityWithRulesStub extends Entity
{
    /**
     * Primary id
     *
     * @var string
     *
     * @ORM\Column(type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;

    /**
     * @var mixed[]
     */
    private $rules;

    /**
     * Get validation rules.
     *
     * @return mixed[]|null
     */
    public function getRules(): ?array
    {
        return $this->rules;
    }

    /**
     * Initialise validation rules.
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\EntityWithRulesStub
     */
    public function setRules(): self
    {
        $this->rules = [];

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [];
    }
}
