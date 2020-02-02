<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Bridge\Laravel;

use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface;
use Eonx\TestUtils\Stubs\BaseStub;

class EventDispatcherStub extends BaseStub implements EventDispatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function dispatch($event, $payload = null, ?bool $halt = null): ?array
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());

        return $this->returnOrThrowResponse(__FUNCTION__, null);
    }

    /**
     * {@inheritdoc}
     */
    public function listen(array $events, string $listener): void
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
    }
}
