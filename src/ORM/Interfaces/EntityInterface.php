<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

use EoneoPay\Utils\Interfaces\SerializableInterface;

interface EntityInterface extends SerializableInterface
{
    /**
     * Get entity id.
     *
     * @return null|string|int
     */
    public function getId();
}
