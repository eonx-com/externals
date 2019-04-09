<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\ResolveTargetEntityExtension;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\ResolveTargetEntityExtension
 */
class ResolveTargetEntityExtensionTest extends TestCase
{
    /**
     * Test extension is registered
     *
     * @return void
     */
    public function testExtension(): void
    {
        $eventManager = new EventManagerStub();
        $rtel = new ResolveTargetEntityExtension(new ResolveTargetEntityListener());

        self::assertCount(0, $eventManager->getSubscribers());

        $rtel->addSubscribers($eventManager, new EntityManagerStub());

        self::assertCount(1, $eventManager->getSubscribers());
        self::assertInstanceOf(ResolveTargetEntityListener::class, $eventManager->getSubscribers()[0]);
    }

    /**
     * Extension should return an empty array when getting filters.
     *
     * @return void
     */
    public function testGetFiltersReturnsEmptyArray(): void
    {
        self::assertEmpty((new ResolveTargetEntityExtension(new ResolveTargetEntityListener()))->getFilters());
    }
}
