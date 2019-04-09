<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

use Doctrine\Common\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository
{
    /**
     * Counts entities by a set of criteria.
     *
     * @param mixed[]|null $criteria
     *
     * @return int The cardinality of the objects that match the given criteria.
     */
    public function count(?array $criteria = null): int;

    /**
     * Finds a single object by a set of criteria.
     *
     * @param mixed[] $criteria The criteria.
     * @param string[]|null $orderBy
     *
     * @return mixed The entity if found, otherwise null.
     */
    public function findOneBy(array $criteria, ?array $orderBy = null);
}
