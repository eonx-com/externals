<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;

interface EntityManagerInterface
{
    /**
     * Flush unit of work to the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function flush(): void;

    /**
     * Generate a unique value based on provided field.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     * @param string $field
     * @param int|null $length
     *
     * @return string
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException
     */
    public function generateRandomUniqueValue(
        EntityInterface $entity,
        string $field,
        ?int $length = null
    ): string;

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface;

    /**
     * Gets the repository from a entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface
     */
    public function getRepository(string $class): RepositoryInterface;

    /**
     * Merge entity to the database, similar to REPLACE INTO in SQL
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to merge into the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function merge(EntityInterface $entity): void;

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to persist to the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function persist(EntityInterface $entity): void;

    /**
     * Remove entity from the database.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to remove from the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function remove(EntityInterface $entity): void;
}
