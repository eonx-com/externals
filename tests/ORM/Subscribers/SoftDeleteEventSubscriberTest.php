<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Subscribers;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber;
use Tests\EoneoPay\Externals\ORMTestCase;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\Common\Persistence\ObjectManagerStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;

/**
 * @covers \EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber
 */
class SoftDeleteEventSubscriberTest extends ORMTestCase
{
    /**
     * EventSubscriber should return expected list of events.
     *
     * @return void
     */
    public function testGetSubscribedEventsReturnsExpectedListOfEvents(): void
    {
        $expected = ['loadClassMetadata', 'onFlush', 'postFlush'];

        self::assertEquals($expected, $this->createInstance()->getSubscribedEvents());
    }

    /**
     * Test nothing happens if the entity manager isn't provided
     *
     * @return void
     *
     * @throws \Exception If datetime contains and invalid string
     */
    public function testOnFlushDoesNotProceedWithoutEntityManager(): void
    {
        // Create entity manager
        $entityManager = new EntityManagerStub();

        // Add entity to manager
        $entity = new EntityStub();

        // Scheduled entity removal
        $entityManager->remove($entity);

        // Call flush with an invalid entity manager
        $this->createInstance()->onFlush(new LifecycleEventArgs($entity, new ObjectManagerStub()));

        // Check nothing was changed on flush
        self::assertFalse($entity->isDeleted());
    }

    /**
     * Test onflush intercepts the delete request
     *
     * @return void
     *
     * @throws \Exception If datetime contains and invalid string
     */
    public function testOnFlushInterceptsDeleteRequest(): void
    {
        // Create entity manager
        $entityManager = $this->getDoctrineEntityManager();

        // Add entity to manager
        $entity = new EntityStub();
        $entityManager->persist($entity);
        $entityManager->flush();

        // Remove entity (softly)
        $entityManager->remove($entity);

        // Ensure the remove hasn't set deletedAt
        self::assertFalse($entity->isDeleted());

        // Call flush
        $this->createInstance()->onFlush(new OnFlushEventArgs($entityManager));

        // Check entity deletedAt has been set
        self::assertTrue($entity->isDeleted());
    }

    /**
     * Test post flush removes entity tracking
     *
     * @return void
     *
     * @throws \Exception If datetime contains and invalid string
     */
    public function testPostFlushDetachesFromEntityManager(): void
    {
        // Create entity manager
        $entityManager = $this->getDoctrineEntityManager();

        // Add entity to manager
        $entity = new EntityStub();
        $entityManager->persist($entity);

        // Check entity manager is tracking entity
        self::assertTrue($entityManager->contains($entity));

        // Flush to save entity
        $entityManager->flush();

        // Remove entity (softly)
        $entityManager->remove($entity);

        // Flush to remove entity
        $entityManager->flush();

        // Check entity deletedAt has been set
        self::assertTrue($entity->isDeleted());

        // Check entity manager is no longer tracking entity
        self::assertFalse($entityManager->contains($entity));
    }

    /**
     * Test soft vs hard delete functionality
     *
     * @return void
     */
    public function testSoftVsHardDeleteFunctionality(): void
    {
        // Create entity manager
        $entityManager = $this->getDoctrineEntityManager();

        // Add entity to manager
        $entity = new EntityStub();
        $entityManager->persist($entity);
        $entityManager->flush();

        // Ensure entity can be found
        $repository = $entityManager->getRepository(EntityStub::class);
        self::assertSame($entity, $repository->find($entity->getEntityId()));

        // Remove entity (softly)
        $entityManager->remove($entity);

        // Flush to remove entity
        $entityManager->flush();

        // Ensure entity can't be found
        self::assertNull($repository->find($entity->getEntityId()));

        // Disable soft-delete filter and find again
        $entityManager->getFilters()->disable('soft-deleteable');
        $deleted = $repository->find($entity->getEntityId());
        self::assertSame($entity->getEntityId(), $deleted->getEntityId());

        // Remove deleted entity entirely
        $entityManager->remove($deleted);
        $entityManager->flush();

        // Ensure it's gone gone
        self::assertNull($deleted->getEntityId());
    }

    /**
     * Test soft delete subscriber instance
     *
     * @return \EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber
     */
    private function createInstance(): SoftDeleteEventSubscriber
    {
        return new SoftDeleteEventSubscriber();
    }
}
