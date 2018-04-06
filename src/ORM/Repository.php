<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use EoneoPay\External\ORM\Interfaces\RepositoryInterface;

class Repository extends SimpleOrmDecorator implements RepositoryInterface
{
    /**
     * Create a new repository from a Doctrine Repository
     *
     * @param \Doctrine\Common\Persistence\ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
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
     * Get all records from a repository
     *
     * @return array|null
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findAll(): ?array
    {
        return $this->callMethod('findAll') ?? [];
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

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias
     * @param string|null $indexBy
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    protected function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->callMethod('createQueryBuilder', $alias, $indexBy);
    }
}
