<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use Doctrine\ORM\QueryBuilder;
use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Exceptions\RepositoryClassNotFoundException;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use Tests\EoneoPay\Externals\DoctrineTestCase;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStubWithCustomRepository;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStubWithNotFoundRepository;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithGetFillableStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithNoEntityAnnotationStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithValidationStub;
use Tests\EoneoPay\Externals\ORM\Stubs\Exceptions\EntityValidationFailedExceptionStub;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) This tests the full functionality of the EntityManager
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Manager itself is complex so lot of tests to perform
 */
class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Custom repository should be able to call createQueryBuilder() even though it's protected
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCustomRepository(): void
    {
        /** @var \Tests\EoneoPay\Externals\ORM\Stubs\EntityCustomRepository $repository */
        $repository = $this->getEntityManager()->getRepository(EntityStubWithCustomRepository::class);
        $queryBuilder = $repository->getQueryBuilder();

        self::assertTrue(\method_exists($repository, 'createQueryBuilder'));
        /** @noinspection UnnecessaryAssertionInspection Testing actual value returned */
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
     */
    public function testEntityManagerWrapsExceptionIntoORMException(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->persist(new EntityWithNoEntityAnnotationStub());
        $this->getEntityManager()->flush();
    }

    /**
     * Test filters collection methods enable/disable filters on entity manager.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testFiltersCollectionMethodsSuccessful(): void
    {
        $filters = $this->getEntityManager()->getFilters();

        $filters->enable('soft-deleteable');
        $filters->disable('soft-deleteable');

        /** @noinspection UnnecessaryAssertionInspection Test of actual returned instance */
        self::assertInstanceOf(FilterCollectionInterface::class, $filters);
    }

    /**
     * Test generating random unique strings checking entity field values
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     *
     * @covers \EoneoPay\Externals\ORM\EntityManager::generateRandomUniqueValue
     */
    public function testGeneratingRandomUniqueValue(): void
    {
        /** @var \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface $repository */
        $value = $this->getEntityManager()->generateRandomUniqueValue(EntityStub::class, 'integer');
        self::assertNotNull($value);
        self::assertEquals(\strlen($value), 16);

        // Check value changes on second generate
        self::assertNotSame(
            $value,
            $this->getEntityManager()->generateRandomUniqueValue(EntityStub::class, 'integer')
        );

        // Check 'length' is respected
        self::assertEquals(\strlen(
            $this->getEntityManager()->generateRandomUniqueValue(EntityStub::class, 'integer', 10)
        ), 10);
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

        /** @noinspection UnnecessaryAssertionInspection Test of actual returned instance */
        self::assertInstanceOf(FilterCollectionInterface::class, $filters);
    }

    /**
     * Test entity manager merge data into new entity from database.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testMergeEntityWithDatabaseSuccessful(): void
    {
        $entity = new EntityStub(['string' => 'string', 'integer' => 1]);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $newEntity = (new EntityStub(['string' => 'string_merged']))->setEntityId((string)$entity->getId());

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
     */
    public function testPersistAndRemoveSuccessful(): void
    {
        // Use entity with getFillable to cover LoggableEventSubscriber
        $entity = new EntityWithGetFillableStub(['string' => 'string', 'integer' => 1]);

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
     */
    public function testPersistWithValidationFailedException(): void
    {
        $this->expectException(EntityValidationFailedExceptionStub::class);

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
     */
    public function testRepositoryMethods(): void
    {
        $this->getEntityManager()->persist(new EntityStub(['string' => 'string', 'integer' => 1]));
        $this->getEntityManager()->flush();

        $repository = $this->getEntityManager()->getRepository(EntityStub::class);

        self::assertInstanceOf(EntityStub::class, $repository->findOneBy(['string' => 'string']));
        self::assertCount(1, $repository->findAll());
        self::assertCount(1, $repository->findBy(['string' => 'string']));
        self::assertEquals(1, $repository->count());
    }

    /**
     * Test simple orm decorator wraps Doctrine exceptions into its own ORMException.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testSimpleOrmDecoratorExceptionWrapsExceptions(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->getFilters()->enable('invalid');
    }
}
