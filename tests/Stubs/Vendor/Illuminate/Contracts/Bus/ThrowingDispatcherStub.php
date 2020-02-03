<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Bus;

use Exception;
use Illuminate\Contracts\Bus\Dispatcher;

/**
 * @coversNothing
 */
class ThrowingDispatcherStub implements Dispatcher
{
    /**
     * {@inheritdoc}
     */
    public function dispatch($command)
    {
        throw new Exception('Something happened');
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchNow($command, $handler = null)
    {
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
