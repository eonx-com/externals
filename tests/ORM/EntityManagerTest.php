<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM;

use Doctrine\ORM\QueryBuilder;
use EoneoPay\External\ORM\Exceptions\DefaultEntityValidationFailedException;
use EoneoPay\External\ORM\Exceptions\ORMException;
use EoneoPay\External\ORM\Exceptions\RepositoryClassNotFoundException;
use EoneoPay\External\ORM\Interfaces\Query\FilterCollectionInterface;
use Tests\EoneoPay\External\DoctrineTestCase;
use Tests\EoneoPay\External\ORM\Stubs\EntityCustomRepository;
use Tests\EoneoPay\External\ORM\Stubs\EntityStub;
use Tests\EoneoPay\External\ORM\Stubs\EntityStubWithCustomRepository;
use Tests\EoneoPay\External\ORM\Stubs\EntityStubWithNotFoundRepository;
use Tests\EoneoPay\External\ORM\Stubs\EntityWithValidationStub;
use Tests\EoneoPay\External\ORM\Stubs\ParentEntityStub;

/**
 * This TestCase also cover FiltersCollection and Repository.
 */
class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Custom repository should be able to call "createQueryBuilder" method and "createQueryBuilder" method is not public.
     *
     * @return void
     *
     * @throws ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCustomRepository(): void
    {
        /** @var EntityCustomRepository $repository */
        $repository = $this->getEntityManager()->getRepository(EntityStubWithCustomRepository::class);
        $queryBuilder = $repository->getQueryBuilder();

        self::assertTrue(\method_exists($repository, 'createQueryBuilder'));
        self::assertEquals(false, \is_callable('createQueryBuilder', false, $repository));
        self::assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    /**
     * Test when custom repository is not found, a "RepositoryNotFoundException" will be thrown.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCustomRepositoryNotFoundException(): void
    {
        $this->expectException(RepositoryClassNotFoundException::class);

        $this->getEntityManager()->getRepository(EntityStubWithNotFoundRepository::class);
    }

    /**
     * Test entity manager should wrap Doctrine exceptions into its own ORMException.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function testEntityManagerWrapsExceptionIntoORMException(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->persist(new ParentEntityStub());
        $this->getEntityManager()->flush();
    }

    /**
     * Test filters collection methods enable/disable filters on entity manager.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function testFiltersCollectionMethodsSuccessful(): void
    {
        $filters = $this->getEntityManager()->getFilters();

        $filters->enable('soft-deleteable');
        $filters->disable('soft-deleteable');

        self::assertInstanceOf(FilterCollectionInterface::class, $filters);
    }

    /**
     * Test entity manager get filters returns our filters collection.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testGetFiltersReturnRightCollection(): void
    {
        $filters = $this->getEntityManager()->getFilters();
        self::assertInstanceOf(FilterCollectionInterface::class, $filters);
    }

    /**
     * Test entity manager merge data into new entity from database.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testMergeEntityWithDatabaseSuccessful(): void
    {
        $entity = new EntityStub(['string' => 'string', 'integer' => 1]);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $newEntity = (new EntityStub(['string' => 'string_merged']))->setEntityId($entity->getId());

        $this->getEntityManager()->merge($newEntity);

        self::assertEquals($entity->toArray(), $newEntity->toArray());
    }

    /**
     * Test entity manager persist and remove records successfully.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testPersistAndRemoveSuccessful(): void
    {
        $entity = new EntityStub(['string' => 'string', 'integer' => 1]);

        // Persist entity into database
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $entityId = $entity->getId();

        // Test record is present in database
        self::assertNotNull($entityId);
        self::assertInstanceOf(
            EntityStub::class,
            $this->getEntityManager()->getRepository(EntityStub::class)->find($entityId)
        );

        // Remove record from database
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        // Test record has been removed
        self::assertNull($this->getEntityManager()->getRepository(EntityStub::class)->find($entityId));
    }

    /**
     * Test entity manager should throw exception when validation failed on entity.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function testPersistWithValidationFailedException(): void
    {
        $this->expectException(DefaultEntityValidationFailedException::class);

        $this->getEntityManager()->persist(new EntityWithValidationStub());
        $this->getEntityManager()->flush();
    }

    /**
     * Test repository methods retrieve record from database.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function testRepositoryMethods(): void
    {
        $this->getEntityManager()->persist(new EntityStub(['string' => 'string', 'integer' => 1]));
        $this->getEntityManager()->flush();

        $repository = $this->getEntityManager()->getRepository(EntityStub::class);

        self::assertInstanceOf(EntityStub::class, $repository->findOneBy(['string' => 'string']));
        self::assertCount(1, $repository->findAll());
        self::assertCount(1, $repository->findBy(['string' => 'string']));
    }

    /**
     * Test simple orm decorator wraps Doctrine exceptions into its own ORMException.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function testSimpleOrmDecoratorExceptionWrapsExceptions(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->getFilters()->enable('invalid');
    }
}
