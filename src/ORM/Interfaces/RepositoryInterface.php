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
     * {@inheritdoc}
     *
     * @param string[]|null $orderBy Optional order to sort by before returning
     */
    public function findOneBy(array $criteria, ?array $orderBy = null);
}
