<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\Exceptions\EntityValidationException;
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If entity validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If entity validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If entity validation fails
     */
    public function persist(Entity $entity): void
    {
        $this->callMethod('persist', $entity);
    }

    /**
     * Remove entity from the database.
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to remove from the database
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If entity validation fails
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    public function remove(Entity $entity): void
    {
        $this->callMethod('remove', $entity);
    }

    /**
     * Call a method on the entity manager and catch any exception
     *
     * @param string $method The method to call
     * @param mixed $parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException If entity validation fails
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->entityManager, $method], $parameters ?? []);
        } catch (Exception $exception) {
            // Throw directly exceptions from this package
            if ($exception instanceof EntityValidationException) {
                throw $exception;
            }

            // Wrap others in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), 0, $exception);
        }
    }
}
