<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Query;

use Doctrine\ORM\Query\FilterCollection as DoctrineFilterCollection;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use EoneoPay\Externals\ORM\SimpleOrmDecorator;

class FilterCollection extends SimpleOrmDecorator implements FilterCollectionInterface
{
    /**
     * Create a new filter collection from a Doctrine FilterCollection.
     *
     * @param \Doctrine\ORM\Query\FilterCollection $filterCollection
     */
    public function __construct(DoctrineFilterCollection $filterCollection)
    {
        $this->decorated = $filterCollection;
    }

    /**
     * Disables a filter.
     *
     * @param string $name Name of the filter.
     *
     * @return void.
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If the filter does not exist.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function disable($name): void
    {
        $this->callMethod('disable', $name);
    }

    /**
     * Enables a filter from the collection.
     *
     * @param string $name Name of the filter.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If the filter does not exist.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function enable($name): void
    {
        $this->callMethod('enable', $name);
    }
}
