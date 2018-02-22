<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM;

use Tests\EoneoPay\External\DoctrineTestCase;
use Tests\EoneoPay\External\ORM\Stubs\EntityStub;

class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Test persist function on entity manager.
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testPersist(): void
    {
        $entity = new EntityStub([
            'string' => 'string',
            'integer' => 1
        ]);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        self::assertNotNull($entity->getId());
    }
}
