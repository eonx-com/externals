<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Extensions;

use EoneoPay\Externals\ORM\Extensions\SoftDeleteExtension;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Extensions\SoftDeleteExtension
 */
class SoftDeleteExtensionTest extends TestCase
{
    /**
     * LoggableExtension should add subscriber to Doctrine event manager when calling addSubscribers.
     *
     * @return void
     */
    public function testEventSubscriberIsSetInEventManager(): void
    {
        $eventManager = new EventManagerStub();
        $softDeleteable = new SoftDeleteExtension();

        self::assertCount(0, $eventManager->getSubscribers());

        $softDeleteable->addSubscribers($eventManager, new EntityManagerStub());

        self::assertCount(1, $eventManager->getSubscribers());
    }

    /**
     * Extension should register a soft deleteable filter
     *
     * @return void
     */
    public function testGetFiltersReturnsSoftDeleteFilter(): void
    {
        self::assertSame(
            ['soft-deleteable' => SoftDeleteableFilter::class],
            (new SoftDeleteExtension())->getFilters()
        );
    }
}
