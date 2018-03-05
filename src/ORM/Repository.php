<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\ORM\EntityRepository;
use EoneoPay\External\ORM\Interfaces\RepositoryInterface;

class Repository extends SimpleOrmDecorator implements RepositoryInterface
{
    /**
     * Create a new repository from a Doctrine Repository
     *
     * @param \Doctrine\ORM\EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->decorated = $repository;
    }

    /**
     * Find an entity by its primary key / identifier
     *
     * @param mixed $entityId The primary identifier for the entity
     *
     * @return mixed Associated entity on success, null if not found
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function find($entityId)
    {
        return $this->callMethod('find', $entityId);
    }

    /**
     * Finds entities which match a set of criteria
     *
     * @param array $criteria Array of criteria to find by
     *
     * @return array
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findBy(array $criteria): array
    {
        return $this->callMethod('findBy', $criteria) ?? [];
    }

    /**
     * Finds a single entity by a set of criteria
     *
     * @param array $criteria Array of criteria
     *
     * @return mixed Associated entity on success, null if not found
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findOneBy(array $criteria)
    {
        return $this->callMethod('findOneBy', $criteria);
    }
}
