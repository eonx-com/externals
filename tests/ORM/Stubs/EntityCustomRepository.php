<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\QueryBuilder;
use EoneoPay\Externals\ORM\Repository;

class EntityCustomRepository extends Repository
{
    /**
     * Test only method in order to achieve 100% coverage.
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }
}
