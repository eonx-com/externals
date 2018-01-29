<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Interfaces;

use EoneoPay\External\ORM\Entity;
use EoneoPay\External\ORM\Repository;

interface EntityManagerInterface
{
    /**
     * Flush unit of work to the database
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Gets the repository from a entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\External\ORM\Repository
     */
    public function getRepository(string $class): Repository;

    /**
     * Merge entity to the database, similar to REPLACE INTO in SQL
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to merge into the database
     *
     * @return void
     */
    public function merge(Entity $entity): void;

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to persist to the database
     *
     * @return void
     */
    public function persist(Entity $entity): void;
}
