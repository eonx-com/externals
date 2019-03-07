<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\EntityFactoryManager;
use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use Tests\EoneoPay\Externals\EntityFactoryManagerTestCase;
use Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityCustomRepository;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithRulesStub;
use Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub;

class EntityFactoryManagerTest extends EntityFactoryManagerTestCase
{
    /**
     * EntityFactoryManager should create successfully entity.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testCreateEntityAndGetDefaultDataSuccessfully(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $entity1 = $entityFactoryManager->get(EntityStub::class);
        $entity2 = $entityFactoryManager->get(EntityStub::class);
        $entity3 = $entityFactoryManager->create(EntityStub::class, ['string' => 'different']);

        foreach ([$entity1, $entity2, $entity3] as $entity) {
            self::assertInstanceOf(EntityStub::class, $entity);
        }

        self::assertEquals(\spl_object_hash($entity1), \spl_object_hash($entity2));
        self::assertNotEquals(\spl_object_hash($entity1), \spl_object_hash($entity3));

        self::assertInstanceOf(EntityWithRulesStub::class, $entityFactoryManager->create(EntityWithRulesStub::class));

        self::assertEquals(
            ['integer' => 1, 'string' => 'string'],
            $entityFactoryManager->getDefaultData(EntityStub::class)
        );
    }

    /**
     * EntityFactory should create default relation entity if provided data is invalid
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testDefaultRelationEntityCreatedIfProvidedDataInvalid(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $child = $entityFactoryManager->create(ChildEntityStub::class, ['parent' => 'invalid']);

        self::assertInstanceOf(ChildEntityStub::class, $child);
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
    }

    /**
     * EntityFactory should create default relation entity if relationships are provided
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testDefaultRelationEntityCreatedIfRelationshipIsProvided(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $parent = new ParentEntityStub();
        $child = $entityFactoryManager->create(ChildEntityStub::class, ['parent' => $parent]);

        self::assertInstanceOf(ChildEntityStub::class, $child);
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
    }

    /**
     * EntityFactory should not create default relation entity data is explicitly set to null.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testDefaultRelationEntityIsNotCreatedIfSetToNull(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $child = $entityFactoryManager->create(ChildEntityStub::class, ['parent' => null]);

        self::assertInstanceOf(ChildEntityStub::class, $child);
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
    }

    /**
     * EntityFactoryManager should throw exception when no factory paths provided.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testEmptyFactoryPathsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EntityFactoryManager($this->getEntityManager(), []);
    }

    /**
     * EntityFactoryManager should throw exception when no namespace mapping configured.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testEmptyNamespaceMappingException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getEntityFactoryManager()->create(EntityStub::class);
    }

    /**
     * EntityFactory should create default relation entity if not set in data.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testHandleDefaultRelationEntitySuccessfully(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $child = $entityFactoryManager->create(ChildEntityStub::class);

        self::assertInstanceOf(ChildEntityStub::class, $child);
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
    }

    /**
     * EntityFactoryManager should throw an exception if entity factory not found.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testNotFoundEntityFactoryException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $entityFactoryManager->create(EntityCustomRepository::class);
    }

    /**
     * Test persist saves entity to database
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function testPersistSavesEntityToDatabase(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $entity = $entityFactoryManager->create(EntityStub::class);
        self::assertNull($entity->getEntityId());

        $entity = $entityFactoryManager->persist(EntityStub::class);
        self::assertNotNull($entity->getEntityId());
    }
}
