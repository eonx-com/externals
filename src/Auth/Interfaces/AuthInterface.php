<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Auth\Interfaces;

use EoneoPay\Externals\ORM\Interfaces\UserInterface;

interface AuthInterface
{
    /**
     * Get current authenticated user entity.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\UserInterface|null
     */
    public function user(): ?UserInterface;
}
