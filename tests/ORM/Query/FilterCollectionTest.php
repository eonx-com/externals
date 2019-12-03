<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Query;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

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

        // If enable/disable doesn't throw exception we're good
        $this->addToAssertionCount(1);
    }

    /**
     * Test an invalid filter throws an exception.
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
