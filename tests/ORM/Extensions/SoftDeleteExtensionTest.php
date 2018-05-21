<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Extensions;

use EoneoPay\Externals\ORM\Extensions\SoftDeleteExtension;
use EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Tests\EoneoPay\Externals\ExtensionsTestCase;

class SoftDeleteExtensionTest extends ExtensionsTestCase
{
    /**
     * Extension should add the event subscriber to the event manager.
     *
     * @return void
     */
    public function testAddSubscriberCallManagerWithRightSubscriber(): void
    {
        $eventManager = $this->mockEventManager();
        $eventManager
            ->shouldReceive('addEventSubscriber')
            ->once()
            ->withArgs(function ($subscriber): bool {
                return $subscriber instanceof SoftDeleteEventSubscriber;
            })
            ->andReturnNull();
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $this->mockEntityManager();
        /** @var \Doctrine\Common\EventManager $eventManager */
        (new SoftDeleteExtension())->addSubscribers($eventManager, $entityManager);
        // Assertions are made using mockery
        self::assertTrue(true);
    }

    /**
     * Extension should return array with soft delete filter when getting list of provided filters.
     *
     * @return void
     */
    public function testGetFiltersReturnExpectedArray(): void
    {
        $filters = (new SoftDeleteExtension())->getFilters();
        $key = 'soft-deleteable';

        self::assertArrayHasKey($key, $filters);
        self::assertEquals(SoftDeleteableFilter::class, $filters[$key]);
    }
}
