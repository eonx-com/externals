<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface EntityInterface
{
    /**
     * Get entity id.
     *
     * @return null|string|int
     */
    public function getId();
}
