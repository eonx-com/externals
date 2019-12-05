<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Auth;

use EoneoPay\Externals\Bridge\Laravel\Auth;
use EoneoPay\Externals\ORM\Interfaces\UserInterface;
use Illuminate\Contracts\Auth\Factory;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Auth\AuthStub as FactoryStub;

/**
 * @coversNothing
 *
 * @method void setDefaultDriver($name)
 */
class AuthStub extends Auth
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\UserInterface|null
     */
    private $user;

    /**
     * Create auth library.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\UserInterface|null $entity The entity to return when calling user
     * @param \Illuminate\Contracts\Auth\Factory|null $factory Auth factory to use
     */
    public function __construct(?UserInterface $entity = null, ?Factory $factory = null)
    {
        parent::__construct($factory ?? new FactoryStub());

        $this->user = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function user(): ?UserInterface
    {
        return $this->user;
    }
}
