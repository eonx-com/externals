<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use EoneoPay\Externals\ORM\Entity;

class EntityWithRulesStub extends Entity
{
    /**
     * @var array
     */
    private $rules;

    /**
     * Get validation rules.
     *
     * @return array|null
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
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
