<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

/**
 * Interface to be implemented by any entity
 * that is acting as an user.
 */
interface UserInterface
{
    /**
     * Get entity id.
     *
     * @return int|string|null
     */
    public function getId();
}
