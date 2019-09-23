<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;
use EoneoPay\Externals\ORM\Query\FilterCollection;
use Exception;

final class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager.
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(DoctrineEntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function findByIds(string $class, array $ids): array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select('e');
        $builder->from($class, 'e');

        $metadata = $this->entityManager->getClassMetadata($class);

        try {
            $field = \sprintf('e.%s', $metadata->getSingleIdentifierFieldName());
            // @codeCoverageIgnoreStart
        } catch (MappingException $exception) {
            // Exception only thrown when composite identifiers are used
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
            // @codeCoverageIgnoreEnd
        }

        $builder->where($builder->expr()->in($field, ':ids'));
        $builder->setParameter('ids', $ids);

        return $builder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function flush(): void
    {
        $this->callMethod('flush');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollection($this->entityManager->getFilters());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     */
    public function getRepository(string $class)
    {
        // Create repository
        $repository = $this->entityManager->getRepository($class);

        // If repository doesn't implement interface, throw exception
        if (($repository instanceof RepositoryInterface) === false) {
            throw new RepositoryClassDoesNotImplementInterfaceException(\sprintf(
                'Repository %s does not implement interface %s',
                \get_class($repository),
                RepositoryInterface::class
            ));
        }

        /**
         * @var \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface $repository
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
        return $repository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function merge(EntityInterface $entity): void
    {
        $this->callMethod('merge', $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function persist(EntityInterface $entity): void
    {
        $this->callMethod('persist', $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function remove(EntityInterface $entity): void
    {
        $this->callMethod('remove', $entity);
    }

    /**
     * Call a method on the entity manager and catch any exception.
     *
     * @param string $method The method to call
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->entityManager, $method], $parameters ?? []);
        } catch (Exception $exception) {
            // Throw directly exceptions from this package
            if ($exception instanceof EntityValidationFailedExceptionInterface) {
                throw $exception;
            }

            // Wrap others in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }
    }
}
