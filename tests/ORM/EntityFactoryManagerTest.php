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
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
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

        self::assertEquals(\spl_object_id($entity1), \spl_object_id($entity2));
        self::assertNotEquals(\spl_object_id($entity1), \spl_object_id($entity3));

        /** @noinspection UnnecessaryAssertionInspection Create returns EntityInterface */
        self::assertInstanceOf(EntityWithRulesStub::class, $entityFactoryManager->create(EntityWithRulesStub::class));

        self::assertEquals(
            ['integer' => 1, 'string' => 'string'],
            $entityFactoryManager->getDefaultData(EntityStub::class)
        );
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
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
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
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testHandleDefaultRelationEntitySuccessfully(): void
    {
        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $child = $entityFactoryManager->create(ChildEntityStub::class);

        /** @noinspection UnnecessaryAssertionInspection Returns EntityInterface */
        self::assertInstanceOf(ChildEntityStub::class, $child);
        /** @var \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub $child */
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
    }

    /**
     * EntityFactoryManager should throw an exception if entity factory not found.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testNotFoundEntityFactoryException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $entityFactoryManager = $this->getEntityFactoryManager([
            'Tests\EoneoPay\Externals\ORM\Stubs\Factories\\' => 'Tests\EoneoPay\Externals\ORM\Stubs'
        ]);

        $entityFactoryManager->create(EntityCustomRepository::class);
    }
}
