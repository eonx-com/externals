<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM;

use Doctrine\DBAL\DBALException;

/**
 * @coversNothing
 */
class TroubledEntityManagerStub extends EntityManagerStub
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getConnection()
    {
        throw new DBALException('I have issues.');
    }
}
