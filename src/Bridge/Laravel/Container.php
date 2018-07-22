<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Container\Interfaces\ContainerInterface;
use Illuminate\Contracts\Container\Container as IlluminateContainer;

class Container implements ContainerInterface
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    private $container;

    /**
     * Container constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(IlluminateContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $serviceId Identifier of the entry to look for.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     *
     * @phpcsSuppress EoneoPay.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $serviceId Identifier of the entry to look for.
     *
     * @return bool
     *
     * @phpcsSuppress EoneoPay.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function has($serviceId): bool
    {
        return $this->container->has($serviceId);
    }
}
