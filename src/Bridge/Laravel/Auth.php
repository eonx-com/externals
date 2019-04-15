<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

/**
 * @method mixed guard(?string $name = null)
 * @method void shouldUse(string $name)
 */
abstract class Auth implements AuthInterface
{
    /**
     * @var \Illuminate\Contracts\Auth\Factory
     */
    private $auth;

    /**
     * Create new authentication instance
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth Illuminate auth factory
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Passes through all unimplemented auth methods to the base auth class
     *
     * @param string $method The method being called
     * @param mixed[] $arguments Any arguements passed to the method
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        $callable = [$this->auth, $method];

        // Call method otherwise return null if not callable
        return \is_callable($callable) === true ? $callable(...$arguments) : null;
    }
}
