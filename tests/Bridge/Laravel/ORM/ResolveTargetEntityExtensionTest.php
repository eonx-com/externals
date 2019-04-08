<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\ORM;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use EoneoPay\Externals\Bridge\Laravel\ORM\ResolveTargetEntityExtension;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\ORM\ResolveTargetEntityExtension
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
        $rtel = new ResolveTargetEntityListener();
        $extension = new ResolveTargetEntityExtension($rtel);
        static::assertEmpty($extension->getFilters());

        $eventManager = new EventManagerStub();
        $extension->addSubscribers($eventManager, new EntityManagerStub());

        static::assertContains($rtel, $eventManager->getSubscribers());
    }
}
