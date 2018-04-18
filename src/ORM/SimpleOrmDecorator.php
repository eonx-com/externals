<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\ORMException;
use Exception;

abstract class SimpleOrmDecorator
{
    /**
     * The decorated ORM object.
     *
     * @var mixed
     */
    protected $decorated;

    /**
     * Call a method on the entity manager and catch any exception
     *
     * @param string $method The method to call
     * @param mixed $parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException If database returns an error
     */
    protected function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->decorated, $method], $parameters ?? []);
        } catch (Exception $exception) {
            // Wrap exceptions in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, $exception);
        }
    }
}
