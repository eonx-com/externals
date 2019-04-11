<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcher;

final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $dispatcher;

    /**
     * Create event dispatcher
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function __construct(IlluminateDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event, $payload = null, ?bool $halt = null): ?array
    {
        return $this->dispatcher->dispatch($event, $payload, $halt ?? false);
    }

    /**
     * {@inheritdoc}
     */
    public function listen(array $events, string $listener): void
    {
        $this->dispatcher->listen($events, $listener);
    }
}
