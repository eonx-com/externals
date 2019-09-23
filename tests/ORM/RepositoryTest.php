<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Repositories\RepositoryStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Repository
 */
class RepositoryTest extends ORMTestCase
{
    /**
     * Test count works as expected.
     *
     * @return void
     */
    public function testCount(): void
    {
        $entityManager = $this->getDoctrineEntityManager();

        // Add several records to database
        $entity1 = new EntityStub(['string' => 'a', 'integer' => 1]);
        $entity2 = new EntityStub(['string' => 'b', 'integer' => 1]);
        $entity3 = new EntityStub(['string' => 'c', 'integer' => 1]);
        $entity4 = new EntityStub(['string' => 'd', 'integer' => 2]);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->persist($entity3);
        $entityManager->persist($entity4);

        $entityManager->flush();

        $repository = $this->createInstance(EntityStub::class);

        // Test count all
        self::assertSame(4, $repository->count());

        // Test count with criteria
        self::assertSame(3, $repository->count(['integer' => 1]));
    }

    /**
     * Test create query builder.
     *
     * @return void
     */
    public function testCreateQueryBuilder(): void
    {
        /** @var \Tests\EoneoPay\Externals\Stubs\ORM\Repositories\RepositoryStub $repository */
        $repository = $this->createInstance(EntityStub::class);

        self::assertSame(['u'], $repository->getQueryBuilder()->getAllAliases());
    }

    /**
     * Test exceptions thrown by doctrine are converted by the repository.
     *
     * @return void
     */
    public function testExceptionsFromDoctrineAreConverted(): void
    {
        $repository = $this->createInstance(EntityStub::class);

        $this->expectException(ORMException::class);

        $repository->count(['invalidProperty' => 1]);
    }

    /**
     * Test find functionality.
     *
     * @return void
     */
    public function testFind(): void
    {
        $entityManager = $this->getDoctrineEntityManager();

        // Add two records to database
        $entity1 = new EntityStub(['string' => 'a', 'integer' => 1]);
        $entity2 = new EntityStub(['string' => 'b', 'integer' => 1]);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);

        $entityManager->flush();

        $repository = $this->createInstance(EntityStub::class);

        // Test find
        self::assertSame($entity1, $repository->find($entity1->getEntityId()));
    }

    /**
     * Test find all functionality.
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $entityManager = $this->getDoctrineEntityManager();

        // Add several records to database
        $entity1 = new EntityStub(['string' => 'a', 'integer' => 1]);
        $entity2 = new EntityStub(['string' => 'b', 'integer' => 1]);
        $entity3 = new EntityStub(['string' => 'c', 'integer' => 1]);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->persist($entity3);

        $entityManager->flush();

        $repository = $this->createInstance(EntityStub::class);

        // Test findAll
        self::assertCount(3, $repository->findAll());
        self::assertSame([$entity1, $entity2, $entity3], $repository->findAll());
    }

    /**
     * Test find by functionality.
     *
     * @return void
     */
    public function testFindBy(): void
    {
        $entityManager = $this->getDoctrineEntityManager();

        // Add several records to database
        $entity1 = new EntityStub(['string' => 'a', 'integer' => 1]);
        $entity2 = new EntityStub(['string' => 'b', 'integer' => 1]);
        $entity3 = new EntityStub(['string' => 'c', 'integer' => 1]);
        $entity4 = new EntityStub(['string' => 'c', 'integer' => 5]);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->persist($entity3);
        $entityManager->persist($entity4);

        $entityManager->flush();

        $repository = $this->createInstance(EntityStub::class);

        // Test findBy
        self::assertCount(3, $repository->findBy(['integer' => 1]));
        self::assertSame([$entity4, $entity1, $entity2, $entity3], $repository->findBy([], ['integer' => 'DESC']));
        self::assertSame([$entity1, $entity2], $repository->findBy([], null, 2));
        self::assertSame([$entity2, $entity3, $entity4], $repository->findBy([], null, null, 1));
    }

    /**
     * Test find one by functionality.
     *
     * @return void
     */
    public function testFindOneBy(): void
    {
        $entityManager = $this->getDoctrineEntityManager();

        // Add several records to database
        $entity1 = new EntityStub(['string' => 'a', 'integer' => 1]);
        $entity2 = new EntityStub(['string' => 'b', 'integer' => 1]);

        $entityManager->persist($entity1);
        $entityManager->persist($entity2);

        $entityManager->flush();

        $repository = $this->createInstance(EntityStub::class);

        // Test findBy
        self::assertSame($entity1, $repository->findOneBy(['integer' => 1]));
        self::assertSame($entity2, $repository->findOneBy(['integer' => 1], ['string' => 'DESC']));
    }

    /**
     * Test get class name.
     *
     * @return void
     */
    public function testGetClassName(): void
    {
        $repository = $this->createInstance(EntityStub::class);

        self::assertSame(EntityStub::class, $repository->getClassName());
    }

    /**
     * Create repository instance.
     *
     * @param string $entityClass The class to get the entity for
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface
     */
    private function createInstance(string $entityClass): RepositoryInterface
    {
        $metadata = $this->getDoctrineEntityManager()->getClassMetadata($entityClass);

        return new RepositoryStub($this->getDoctrineEntityManager(), $metadata);
    }
}
