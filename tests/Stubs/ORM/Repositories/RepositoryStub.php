<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Repositories;

use Doctrine\ORM\QueryBuilder;
use EoneoPay\Externals\ORM\Repository;

class RepositoryStub extends Repository
{
    /**
     * Test only method in order to achieve 100% coverage.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }
}
