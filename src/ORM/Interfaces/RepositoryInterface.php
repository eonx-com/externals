<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface RepositoryInterface
{
    /**
     * Counts entities by a set of criteria.
     *
     * @param mixed[]|null $criteria
     *
     * @return int The cardinality of the objects that match the given criteria.
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function count(?array $criteria = null): int;

    /**
     * Find a entity by its primary key / identifier
     *
     * @param mixed $entityId The primary identifier for the entity
     *
     * @return mixed Associated entity on success, null if not found
     */
    public function find($entityId);

    /**
     * Get all records from a repository
     *
     * @return mixed[]
     */
    public function findAll(): array;

    /**
     * Finds entitys which match a set of criteria
     *
     * @param mixed[] $criteria Array of criteria to find by
     * @param array $orderBy Array of criteria to sort on. Use column name as key, and ASC/DESC as value.
     *
     * @return mixed[]
     */
    public function findBy(array $criteria, array $orderBy = null): array;

    /**
     * Finds a single entity by a set of criteria
     *
     * @param mixed[] $criteria Array of criteria
     * @param array $orderBy Array of criteria to sort on. Use column name as key, and ASC/DESC as value.
     *
     * @return mixed Associated entity on success, null if not found
     */
    public function findOneBy(array $criteria, array $orderBy = null);
}
