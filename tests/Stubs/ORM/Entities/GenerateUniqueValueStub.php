<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\Interfaces\Listeners\GenerateUniqueValueInterface;

/**
 * @method string|null getGeneratedValue()
 * @method $this setGeneratedValue(string $value)
 *
 * @ORM\Entity()
 */
class GenerateUniqueValueStub extends Entity implements GenerateUniqueValueInterface
{
    /**
     * Primary id
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;

    /**
     * Generated property
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    protected $generatedValue;

    /**
     * @inheritdoc
     */
    public function areGeneratorsEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function disableGenerators()
    {
    }

    /**
     * @inheritdoc
     */
    public function enableGenerators()
    {
    }

    /**
     * @inheritdoc
     */
    public function getGeneratedProperty(): string
    {
        return 'generatedValue';
    }

    /**
     * @inheritdoc
     */
    public function getGeneratedPropertyLength(): int
    {
        return 9;
    }

    /**
     * @inheritdoc
     */
    public function hasGeneratedPropertyCheckDigit(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'generatedValue' => $this->generatedValue,
            'id' => $this->entityId
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }
}
