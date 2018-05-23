<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface;

abstract class EntityFactory implements EntityFactoryInterface
{
    /**
     * EntityFactoryManagerInterface instance.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface
     */
    protected $factoryManager;

    /**
     * EntityFactory constructor.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface $factoryManager
     */
    public function __construct(EntityFactoryManagerInterface $factoryManager)
    {
        $this->factoryManager = $factoryManager;
    }
}
