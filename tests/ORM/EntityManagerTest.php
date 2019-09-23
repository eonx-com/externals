<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException;
use EoneoPay\Externals\ORM\Query\FilterCollection;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use ReflectionClass;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\CustomRepositoryStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\FillableStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\InvalidRepositoryStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\NoEntityAnnotationStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\ValidatableStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Exceptions\EntityValidationFailedExceptionStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\EntityManager
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test entity manager
 */
class EntityManagerTest extends ORMTestCase
{
    /**
     * Custom repository should be able to call createQueryBuilder() even though it's protected.
     *
     * @return void
     */
    public function testCustomRepository(): void
    {
        $repository = $this->getEntityManager()->getRepository(CustomRepositoryStub::class);

        self::assertTrue(\method_exists($repository, 'createQueryBuilder'));
    }

    /**
     * Test entity manager should wrap Doctrine exceptions into its own ORMException.
     *
     * @return void
     */
    public function testEntityManagerWrapsExceptionIntoORMException(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->persist(new NoEntityAnnotationStub());
        $this->getEntityManager()->flush();
    }

    /**
     * Test entity manager get filters returns our filters collection.
     *
     * @return void
     *
     * @throws \ReflectionException If class or property don't exist
     */
    public function testGetFiltersReturnRightCollection(): void
    {
        $filters = $this->getEntityManager()->getFilters();

        // Expose the underlying filters method
        $class = new ReflectionClass(FilterCollection::class);
        $collection = $class->getProperty('collection');
        $collection->setAccessible(true);

        $filterCollection = $collection->getValue($filters);

        self::assertCount(1, $filterCollection->getEnabledFilters());
        self::assertInstanceOf(SoftDeleteableFilter::class, $filterCollection->getEnabledFilters()['soft-deleteable']);
    }

    /**
     * Test custom repository throw exception if it doesn't implement the right interface.
     *
     * @return void
     */
    public function testInvalidCustomRepositoryThrowsException(): void
    {
        $this->expectException(RepositoryClassDoesNotImplementInterfaceException::class);

        $this->getEntityManager()->getRepository(InvalidRepositoryStub::class);
    }

    /**
     * Test entity manager merge data into new entity from database.
     *
     * @return void
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
     */
    public function testPersistAndRemoveSuccessful(): void
    {
        // Use entity with getFillable to cover LoggableEventSubscriber
        $entity = new FillableStub(['string' => 'string', 'integer' => 1]);

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
     */
    public function testPersistWithValidationFailedException(): void
    {
        $this->expectException(EntityValidationFailedExceptionStub::class);

        $this->getEntityManager()->persist(new ValidatableStub());
        $this->getEntityManager()->flush();
    }

    /**
     * Test repository method findByIds.
     *
     * @return void
     */
    public function testRepositoryMethodFindByIds(): void
    {
        $this->getEntityManager()->persist($entity1 = new EntityStub());
        $this->getEntityManager()->persist($entity2 = new EntityStub());
        $this->getEntityManager()->persist($entity3 = new EntityStub());
        $this->getEntityManager()->flush();

        $ids = [];
        $ids[] = $this->getDoctrineEntityManager()->getClassMetadata(EntityStub::class)
            ->getIdentifierValues($entity1);
        $ids[] = $this->getDoctrineEntityManager()->getClassMetadata(EntityStub::class)
            ->getIdentifierValues($entity2);
        $ids[] = $this->getDoctrineEntityManager()->getClassMetadata(EntityStub::class)
            ->getIdentifierValues($entity3);

        $result = $this->getEntityManager()->findByIds(EntityStub::class, $ids);

        self::assertCount(3, $result);
        self::assertContains($entity1, $result);
        self::assertContains($entity2, $result);
        self::assertContains($entity3, $result);
    }

    /**
     * Test repository methods retrieve record from database.
     *
     * @return void
     */
    public function testRepositoryMethods(): void
    {
        $this->getEntityManager()->persist(new EntityStub(['string' => 'string', 'integer' => 1]));
        $this->getEntityManager()->flush();

        $repository = $this->getEntityManager()->getRepository(EntityStub::class);

        self::assertInstanceOf(EntityStub::class, $repository->findOneBy(['string' => 'string']));
        self::assertCount(1, $repository->findAll());
        self::assertCount(1, $repository->findBy(['string' => 'string']));
        self::assertSame(1, $repository->count());
    }
}
