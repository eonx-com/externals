<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Stubs;

use EoneoPay\External\ORM\Entity;

class InterfaceAndGetRulesStub extends Entity
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
     * @return \Tests\EoneoPay\External\ORM\Stubs\InterfaceAndGetRulesStub
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
