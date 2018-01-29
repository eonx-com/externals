<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\Exceptions\ORMException;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use Exception;

class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Create an internal entity manager
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(DoctrineEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Flush unit of work to the database
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    public function flush(): void
    {
        $this->callMethod('flush');
    }

    /**
     * Gets the repository from an entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\External\ORM\Repository
     */
    public function getRepository(string $class): Repository
    {
        return new Repository($this->entityManager->getRepository($class));
    }

    /**
     * Merge entity to the database, similar to REPLACE in SQL
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to merge to the database
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    public function merge(Entity $entity): void
    {
        $this->callMethod('merge', $entity);
    }

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to persist to the database
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    public function persist(Entity $entity): void
    {
        $this->callMethod('persist', $entity);
    }

    /**
     * Call a method on the entity manager and catch any exception
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
            return \call_user_func_array([$this->entityManager, $method], $parameters ?? []);
        } catch (Exception $exception) {
            throw new ORMException(
                \sprintf('Database Error: %s', $exception->getMessage()),
                null,
                $exception
            );
        }
    }
}
