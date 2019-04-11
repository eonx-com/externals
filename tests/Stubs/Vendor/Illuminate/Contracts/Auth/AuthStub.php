<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Auth;

use Illuminate\Contracts\Auth\Factory;

class AuthStub implements Factory
{
    /**
     * Guards called
     *
     * @var string[]
     */
    private $guards = [];

    /**
     * Uses called
     *
     * @var string[]
     */
    private $uses = [];

    /**
     * Get guards used
     *
     * @return string[]
     */
    public function getGuards(): array
    {
        return $this->guards;
    }

    /**
     * Get uses used
     *
     * @return string[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * {@inheritdoc}
     */
    public function guard($name = null)
    {
        // Only set if name is passed
        if ($name !== null) {
            $this->guards[] = $name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUse($name): void
    {
        $this->uses[] = $name;
    }
}
