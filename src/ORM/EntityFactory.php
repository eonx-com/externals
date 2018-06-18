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
     * Add default entity into data if not set.
     *
     * @param mixed[] $data Given data to create entity
     * @param string $key Attribute name of the relation entity
     * @param string $entityClass Entity class to create if not set
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    protected function handleDefaultRelationEntity(array &$data, string $key, string $entityClass): void
    {
        if ((isset($data[$key]) === false) || \is_array($data[$key])) {
            $data[$key] = $this->factoryManager->create($entityClass, $data[$key] ?? null);
        }
    }
}
