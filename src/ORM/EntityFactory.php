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
    private function isRelationSet(array &$data, string $key): bool
    {
        // If array key doesn't exist, return
        if (\array_key_exists($key, $data) === false) {
            return false;
        }

        // If key is an entity, return
        if (($data[$key] instanceof Entity) === true) {
            return true;
        }

        // If key is null, unset and return - this allows a relationship to be null
        if ($data[$key] === null) {
            unset($data[$key]);

            return true;
        }

        // If data isn't an array, unset
        if (\is_array($data[$key]) === false) {
            unset($data[$key]);
        }

        // Data is an array or not defined
        return false;
    }
}
