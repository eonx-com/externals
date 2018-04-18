<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface RepositoryInterface
{
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
     * @return array|null
     */
    public function findAll(): ?array;

    /**
     * Finds entitys which match a set of criteria
     *
     * @param array $criteria Array of criteria to find by
     *
     * @return array
     */
    public function findBy(array $criteria): array;

    /**
     * Finds a single entity by a set of criteria
     *
     * @param array $criteria Array of criteria
     *
     * @return mixed Associated entity on success, null if not found
     */
    public function findOneBy(array $criteria);
}
