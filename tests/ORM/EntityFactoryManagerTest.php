<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\EntityFactoryManager;
use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use Tests\EoneoPay\Externals\EntityFactoryManagerTestCase;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityCustomRepository;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithRulesStub;

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

        /** @noinspection UnnecessaryAssertionInspection Create returns EntityInterface */
        self::assertInstanceOf(EntityStub::class, $entityFactoryManager->create(EntityStub::class));
        /** @noinspection UnnecessaryAssertionInspection Create returns EntityInterface */
        self::assertInstanceOf(EntityStub::class, $entityFactoryManager->create(EntityStub::class));
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
