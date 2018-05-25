<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface EntityFactoryManagerInterface
{
    /**
     * Add mapping between factories and entities namespace.
     *
     * @param string $factoriesNamespace Namespace where to find entity factories
     * @param string $entitiesNamespace Namespace where to find related entities
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface
     */
    public function addNamespaceMapping(string $factoriesNamespace, string $entitiesNamespace): self;

    /**
     * Create a new entity, persist it and return it.
     *
     * @param string $className
     * @param mixed[]|null $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function create(string $className, ?array $data = null): EntityInterface;

    /**
     * Get the entity from cache or create a new one, persist it and return it.
     *
     * @param string $className
     * @param mixed[]|null $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function get(string $className, ?array $data = null): EntityInterface;

    /**
     * Get entity factory default data based on entity class name.
     *
     * @param string $className
     *
     * @return mixed[]
     */
    public function getDefaultData(string $className): array;

    /**
     * Get entity factory based on entity class name.
     *
     * @param string $className
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface
     */
    public function getEntityFactory(string $className): EntityFactoryInterface;
}
