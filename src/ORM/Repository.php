<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;
use Exception;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * Initialise a new repository
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager Entity manager instance
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata The class descriptor
     */
    public function __construct(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->entityName = $classMetadata->name;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function count(?array $criteria = null): int
    {
        return $this->callMethod('count', $criteria ?? []);
    }

    /**
     * @inheritdoc
     */
    public function find($entityId)
    {
        return $this->entityManager->find($this->entityName, $entityId);
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findAll(): array
    {
        return $this->callMethod('loadAll') ?? [];
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->callMethod('loadAll', $criteria, $orderBy, $limit, $offset) ?? [];
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return $this->callMethod('load', $criteria, null, null, [], null, 1, $orderBy);
    }

    /**
     * @inheritdoc
     */
    public function getClassName(): string
    {
        return $this->entityName;
    }

    /**
     * @inheritdoc
     */
    protected function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select($alias)
            ->from($this->entityName, $alias, $indexBy);
    }

    /**
     * Call a method on the entity manager and catch any exception
     *
     * @param string $method The method torc/ORM/Subscribers/SoftDeleteEventSubscriber.php call
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If EntityManager has an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            // Get persister
            $persister = $this->entityManager->getUnitOfWork()->getEntityPersister($this->entityName);

            return \call_user_func_array([$persister, $method], $parameters ?? []);
        } catch (Exception $exception) {
            // Wrap all thrown exceptions as an ORM exception
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, $exception);
        }
    }
}
