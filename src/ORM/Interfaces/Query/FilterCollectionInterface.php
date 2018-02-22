<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Interfaces\Query;

interface FilterCollectionInterface
{
    /**
     * Disables a filter.
     *
     * @param string $name Name of the filter.
     *
     * @return void.
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If the filter does not exist.
     */
    public function disable($name): void;

    /**
     * Enables a filter from the collection.
     *
     * @param string $name Name of the filter.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If the filter does not exist.
     */
    public function enable($name): void;
}
