<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Illuminate\Contracts\Container\Container as IlluminateContainer;

final class Container implements ContainerInterface
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    private $container;

    /**
     * Create container
     *
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(IlluminateContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @inheritdoc
     */
    public function has($serviceId): bool
    {
        return $this->container->has($serviceId);
    }
}
