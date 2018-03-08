<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\EventDispatcher\Interfaces\EventDispatcherInterface;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcher;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $dispatcher;

    /**
     * EventDispatcher constructor.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function __construct(IlluminateDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param  string|mixed $event
     * @param  mixed $payload
     * @param  bool $halt
     *
     * @return array|null
     */
    public function dispatch($event, $payload = null, ?bool $halt = null): ?array
    {
        return $this->dispatcher->dispatch($event, $payload ?? [], $halt ?? false);
    }
}
