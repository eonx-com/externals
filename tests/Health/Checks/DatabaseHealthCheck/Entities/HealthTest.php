<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Health\Checks\DatabaseHealthCheck\Entities;

use EoneoPay\Externals\Health\Checks\DatabaseHealthCheck\Entities\Health;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\Health\Checks\DatabaseHealthCheck\Entities\Health
 */
class HealthTest extends ORMTestCase
{
    /**
     * Test entity works as entity should, i.e it can be persisted and flushed.
     *
     * @return void
     */
    public function testEntity(): void
    {
        $health = new Health();

        $this->getEntityManager()->persist($health);
        $this->getEntityManager()->flush();

        self::assertSame([], $health->toArray());
        self::assertNotNull($health->getId());
    }
}
