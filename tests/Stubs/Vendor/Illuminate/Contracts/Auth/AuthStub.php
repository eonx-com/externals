<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Auth;

use Illuminate\Contracts\Auth\Factory;

class AuthStub implements Factory
{
    /**
     * Driver in use.
     *
     * @var mixed
     */
    private $driver;

    /**
     * Guards called.
     *
     * @var string[]
     */
    private $guards = [];

    /**
     * Uses called.
     *
     * @var string[]
     */
    private $uses = [];

    /**
     * Get specified driver.
     *
     * @return mixed
     */
    public function getDefaultDriver()
    {
        return $this->driver;
    }

    /**
     * Get guards used.
     *
     * @return string[]
     */
    public function getGuards(): array
    {
        return $this->guards;
    }

    /**
     * Get uses used.
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
     * Set default driver.
     *
     * @param mixed $name
     *
     * @return void
     */
    public function setDefaultDriver($name): void
    {
        $this->driver = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUse($name): void
    {
        $this->uses[] = $name;
    }
}
