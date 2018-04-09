<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Stubs;

use Doctrine\ORM\QueryBuilder;
use EoneoPay\External\ORM\Repository;

class EntityCustomRepository extends Repository
{
    /**
     * Test only method in order to achieve 100% coverage.
     *
     * @return QueryBuilder
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }
}
