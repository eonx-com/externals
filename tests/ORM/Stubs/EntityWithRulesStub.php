<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\Interfaces\ValidatableInterface;
use Tests\EoneoPay\Externals\ORM\Stubs\Exceptions\EntityValidationFailedExceptionStub;

/**
 * @ORM\Entity()
 */
class EntityWithRulesStub extends Entity implements ValidatableInterface
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
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return $this->rules ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationFailedException(): string
    {
        return EntityValidationFailedExceptionStub::class;
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
