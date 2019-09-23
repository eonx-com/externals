<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Query;

use Doctrine\ORM\Query\FilterCollection as DoctrineFilterCollection;
use EoneoPay\Externals\ORM\Exceptions\ORMException;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use InvalidArgumentException;

final class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var \Doctrine\ORM\Query\FilterCollection
     */
    private $collection;

    /**
     * Create a new filter collection from a Doctrine FilterCollection.
     *
     * @param \Doctrine\ORM\Query\FilterCollection $filterCollection
     */
    public function __construct(DoctrineFilterCollection $filterCollection)
    {
        $this->collection = $filterCollection;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If the filter does not exist.
     */
    public function disable($name): void
    {
        $this->callMethod('disable', $name);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If the filter does not exist.
     */
    public function enable($name): void
    {
        $this->callMethod('enable', $name);
    }

    /**
     * Call a method on the entity manager and catch any exception.
     *
     * @param string $method The method to call
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->collection, $method], $parameters ?? []);
        } catch (InvalidArgumentException $exception) {
            // Wrap exceptions in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }
    }
}
