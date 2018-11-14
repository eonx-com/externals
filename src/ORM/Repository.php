<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;

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
     * {@inheritdoc}
     */
    public function count(?array $criteria = null): int
    {
        return $this->callMethod('count', $criteria ?? []);
    }

    /**
     * Find an entity by its primary key / identifier
     *
     * @param mixed $entityId The primary identifier for the entity
     *
     * @return mixed Associated entity on success, null if not found
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function find($entityId)
    {
        return $this->callMethod('find', $entityId);
    }

    /**
     * Get all records from a repository
     *
     * @return mixed[]
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findAll(): array
    {
        return $this->callMethod('findAll') ?? [];
    }

    /**
     * Finds entities which match a set of criteria
     *
     * @param mixed[] $criteria Array of criteria to find by
     * @param array $orderBy Array of criteria to sort on. Use column name as key, and ASC/DESC as value.
     *
     * @return mixed[]
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findBy(array $criteria, array $orderBy = null): array
    {
        return $this->callMethod('findBy', $criteria, $orderBy) ?? [];
    }

    /**
     * Finds a single entity by a set of criteria
     *
     * @param mixed[] $criteria Array of criteria
     * @param array $orderBy Array of criteria to sort on. Use column name as key, and ASC/DESC as value.
     *
     * @return mixed Associated entity on success, null if not found
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->callMethod('findOneBy', $criteria, $orderBy);
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias
     * @param string|null $indexBy
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    protected function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->callMethod('createQueryBuilder', $alias, $indexBy);
    }
}
