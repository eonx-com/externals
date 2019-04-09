<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Auth;

use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;

class AuthStub implements AuthInterface
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null
     */
    private $entity;

    /**
     * Create auth library
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $entity The entity to return when calling user
     */
    public function __construct(?EntityInterface $entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function user(): ?EntityInterface
    {
        return $this->entity;
    }
}
