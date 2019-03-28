<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;
use EoneoPay\Externals\ORM\Query\FilterCollection;
use EoneoPay\Utils\Generator;
use Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) EntityManager is complicated
 */
class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(DoctrineEntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function findByIds(string $class, array $ids): array
    {
        /** @var \Doctrine\ORM\QueryBuilder $builder */
        $builder = $this->callMethod('createQueryBuilder');

        $builder->select('e');
        $builder->from($class, 'e');

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        $meta = $this->callMethod('getClassMetadata', $class);

        try {
            $field = \sprintf('e.%s', $meta->getSingleIdentifierFieldName());
            // @codeCoverageIgnoreStart
        } catch (MappingException $exception) {
            // Exception only thrown when composite identifiers are used
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, $exception);
            // @codeCoverageIgnoreEnd
        }

        $builder->where($builder->expr()->in($field, ':ids'));
        $builder->setParameter('ids', $ids);

        return $builder->getQuery()->getResult();
    }

    /**
     * Flush unit of work to the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function flush(): void
    {
        $this->callMethod('flush');
    }

    /**
     * Generate a unique value based on provided field.
     *
     * @param string $entityClass
     * @param string $field
     * @param int|null $length
     *
     * @return string
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException
     */
    public function generateRandomUniqueValue(
        string $entityClass,
        string $field,
        ?int $length = null
    ): string {
        $generated = new Generator();
        $uniqueValue = null;

        // 100 attempts for uniqueness
        for ($attempts = 0; $attempts < 100; $attempts++) {
            $randomValue = $generated->randomString($length ?? 16);

            // Check repository if the value has already been used
            if ($this->getRepository($entityClass)->count([$field => $randomValue]) === 0) {
                $uniqueValue = $randomValue;
                break;
            }
        }

        if ($uniqueValue === null) {
            // @codeCoverageIgnoreStart
            // Unable to test without undetermined loop size
            throw new ORMException('Uniqueness could not be obtained');
            // @codeCoverageIgnoreEnd
        }

        return $uniqueValue;
    }

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollection($this->entityManager->getFilters());
    }

    /**
     * Gets the repository from an entity class. If custom repository is defined and annotation is set,
     * the custom repository will be returned with the ability to use query builder.
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException If repository is not found for entity
     */
    public function getRepository(string $class): RepositoryInterface
    {
        $metaDataClass = $this->entityManager->getClassMetadata($class);
        $customRepository = $metaDataClass->customRepositoryClassName;

        if (empty($metaDataClass->customRepositoryClassName)) {
            return new Repository($this->entityManager->getRepository($class));
        }

        if (\class_exists($customRepository) === false) {
            throw new RepositoryClassNotFoundException(\sprintf('%s not found', $customRepository));
        }

        $repositoryClass = $this->entityManager->getConfiguration()->getDefaultRepositoryClassName();

        return new $metaDataClass->customRepositoryClassName(
            new $repositoryClass($this->entityManager, $metaDataClass)
        );
    }

    /**
     * Merge entity to the database, similar to REPLACE in SQL
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to merge to the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function merge(EntityInterface $entity): void
    {
        $this->callMethod('merge', $entity);
    }

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to persist to the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function persist(EntityInterface $entity): void
    {
        $this->callMethod('persist', $entity);
    }

    /**
     * Remove entity from the database.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity The entity to remove from the database
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Interfaces\Exceptions\EntityValidationFailedExceptionInterface Validation failure
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    public function remove(EntityInterface $entity): void
    {
        $this->callMethod('remove', $entity);
    }

    /**
     * Call a method on the entity manager and catch any exception
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
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, $exception);
        }
    }
}
