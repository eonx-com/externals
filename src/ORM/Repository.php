<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\ORM\EntityRepository;
use EoneoPay\External\ORM\Exceptions\ORMException;
use EoneoPay\External\ORM\Interfaces\RepositoryInterface;
use Exception;

class Repository implements RepositoryInterface
{
    /**
     * The Doctrine Entity Repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * Create a new repository from a Doctrine Repository
     *
     * @param \Doctrine\ORM\EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
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

    /**
     * Call a method on the repository and catch any exception
     *
     * @param string $method The method to call
     * @param mixed $parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->repository, $method], $parameters ?? []);
        } catch (Exception $exception) {
            throw new ORMException(
                \sprintf('Database Error: %s', $exception->getMessage()),
                null,
                $exception
            );
        }
    }
}
