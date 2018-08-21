<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface;

abstract class EntityFactory implements EntityFactoryInterface
{
    /**
     * EntityFactoryManagerInterface instance.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface
     */
    protected $factoryManager;

    /**
     * EntityFactory constructor.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface $factoryManager
     */
    public function __construct(EntityFactoryManagerInterface $factoryManager)
    {
        $this->factoryManager = $factoryManager;
    }

    /**
     * Create and add default entity into data if not set.
     *
     * @param mixed[] $data Given data to create entity
     * @param string $key Attribute name of the relation entity
     * @param string $entityClass Entity class to create if not set
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    protected function createDefaultRelationEntity(array &$data, string $key, string $entityClass): void
    {
        if ($this->isRelationSet($data, $key) === false) {
            $data[$key] = $this->factoryManager->create($entityClass, $data[$key] ?? null);
        }
    }

    /**
     * Persist and add default entity into data if not set.
     *
     * @param mixed[] $data Given data to create entity
     * @param string $key Attribute name of the relation entity
     * @param string $entityClass Entity class to create if not set
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    protected function persistDefaultRelationEntity(array &$data, string $key, string $entityClass): void
    {
        if ($this->isRelationSet($data, $key) === false) {
            $data[$key] = $this->factoryManager->persist($entityClass, $data[$key] ?? null);
        }
    }

    /**
     * Check if the relation has been set already.
     *
     * @param mixed[] $data Given data to create entity
     * @param string $key Attribute name of the relation entity
     *
     * @return bool
     */
    private function isRelationSet(array $data, string $key): bool
    {
        // Allow null to be passed in the key specifying a relationship should not be created
        return \array_key_exists($key, $data) === true && \is_array($data[$key]) === false;
    }
}
