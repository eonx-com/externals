<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Query;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use Tests\EoneoPay\Externals\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Query\FilterCollection
 */
class FilterCollectionTest extends ORMTestCase
{
    /**
     * Test filters collection methods enable/disable filters on entity manager.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testFiltersCollectionMethodsSuccessful(): void
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->enable('soft-deleteable');
        $filters->disable('soft-deleteable');

        /** @noinspection UnnecessaryAssertionInspection Test of actual returned instance */
        self::assertInstanceOf(FilterCollectionInterface::class, $filters);
    }

    /**
     * Test an invalid filter throws an exception
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testInvalidFilterThrowsException(): void
    {
        $this->expectException(ORMException::class);

        $this->getEntityManager()->getFilters()->enable('invalid');
    }
}
