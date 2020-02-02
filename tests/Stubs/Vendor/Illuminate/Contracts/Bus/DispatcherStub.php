<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Bus;

use Illuminate\Contracts\Bus\Dispatcher;

/**
 * @coversNothing
 */
class DispatcherStub implements Dispatcher
{
    /**
     * @var mixed[]
     */
    private $calls = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch($command)
    {
        $this->calls[] = ['command' => $command];
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchNow($command, $handler = null)
    {
    }

    /**
     * Get calls that have been made to dispatch().
     *
     * @return mixed[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandHandler($command)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasCommandHandler($command)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $map)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function pipeThrough(array $pipes)
    {
    }
}
