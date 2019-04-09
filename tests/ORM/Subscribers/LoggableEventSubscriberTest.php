<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Subscribers;

use Doctrine\ORM\Event\OnFlushEventArgs;
use EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Mapping\Event\Adapter\ORM;
use Tests\EoneoPay\Externals\ORMTestCase;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\FillableStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\NoInterfaceStub;

/**
 * @covers \EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber
 */
class LoggableEventSubscriberTest extends ORMTestCase
{
    /**
     * Test loggable is only performed on entities extending the correct interface
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache extension isn't loaded
     */
    public function testConfigurationNotPerformedOnNonInterfaceEntities(): void
    {
        $configuration = $this->createInstance()
            ->getConfiguration($this->getDoctrineEntityManager(), FillableStub::class);

        self::assertSame(
            ['loggable' => true, 'versioned' => ['entityId', 'integer', 'string']],
            $configuration
        );
    }

    /**
     * Test entity with fillable properties only versions fillable properties
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache extension isn't loaded
     */
    public function testConfigurationOnFillableEntity(): void
    {
        $configuration = $this->createInstance()
            ->getConfiguration($this->getDoctrineEntityManager(), EntityStub::class);

        self::assertSame(
            ['loggable' => true, 'versioned' => ['deletedAt', 'entityId', 'integer', 'string']],
            $configuration
        );
    }

    /**
     * Test getting configuration on a normal entity
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache extension isn't loaded
     */
    public function testConfigurationOnNormalEntity(): void
    {
        $configuration = $this->createInstance()
            ->getConfiguration($this->getDoctrineEntityManager(), NoInterfaceStub::class);

        self::assertSame([], $configuration);
    }

    /**
     * Test creating log entry
     *
     * @return void
     *
     * @throws \Exception Inherited from gedmo logger
     */
    public function testCreateLogEntryExtractsUsername(): void
    {
        // Create loggable adapter with an event
        $adapter = new ORM();
        $adapter->setEventArgs(new OnFlushEventArgs($this->getDoctrineEntityManager()));

        // Test username '1' is returned by log entry
        $entry = $this->createInstance('1')
            ->createLogEntry('insert', new EntityStub(), $adapter);

        self::assertInstanceOf(LogEntry::class, $entry);
        self::assertSame('1', $entry->getUsername());

        // Test default is set if username resolver returns null
        $entry = $this->createInstance()
            ->createLogEntry('insert', new EntityStub(), $adapter);

        self::assertInstanceOf(LogEntry::class, $entry);
        self::assertSame('not_set', $entry->getUsername());
    }

    /**
     * EventSubscriber should return expected list of events.
     *
     * @return void
     */
    public function testGetSubscribedEventsReturnsExpectedListOfEvents(): void
    {
        $expected = ['onFlush', 'loadClassMetadata', 'postPersist'];

        self::assertEquals($expected, $this->createInstance()->getSubscribedEvents());
    }


    /**
     * Create validate subscriber instance
     *
     * @param string|null $username The username to return from the resolver
     *
     * @return \EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber
     */
    private function createInstance(?string $username = null): LoggableEventSubscriber
    {
        return new LoggableEventSubscriber(static function () use ($username): ?string {
            return $username;
        });
    }
}
