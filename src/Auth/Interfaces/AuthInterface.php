<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Auth\Interfaces;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;

interface AuthInterface
{
    /**
     * Get current authenticated user entity.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null
     */
    public function user(): ?EntityInterface;
}
