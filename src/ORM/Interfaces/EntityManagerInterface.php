<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Interfaces;

use EoneoPay\External\ORM\Interfaces\Query\FilterCollectionInterface;

interface EntityManagerInterface
{
    /**
     * Flush unit of work to the database
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \EoneoPay\External\ORM\Interfaces\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface;

    /**
     * Gets the repository from a entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\External\ORM\Interfaces\RepositoryInterface
     */
    public function getRepository(string $class): RepositoryInterface;

    /**
     * Merge entity to the database, similar to REPLACE INTO in SQL
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to merge into the database
     *
     * @return void
     */
    public function merge(EntityInterface $entity): void;

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to persist to the database
     *
     * @return void
     */
    public function persist(EntityInterface $entity): void;

    /**
     * Remove entity from the database.
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to remove from the database
     *
     * @return void
     */
    public function remove(EntityInterface $entity): void;
}
