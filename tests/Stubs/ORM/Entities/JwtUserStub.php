<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use EoneoPay\Externals\ORM\Interfaces\UserInterface;

class JwtUserStub implements UserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUniqueId()
    {
        return 1;
    }
}
