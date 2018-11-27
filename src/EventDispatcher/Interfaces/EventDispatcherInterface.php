<?php
declare(strict_types=1);

namespace EoneoPay\Externals\EventDispatcher\Interfaces;

interface EventDispatcherInterface
{
    /**
     * Fire an event and call the listeners.
     *
     * @param  string|mixed $event
     * @param  mixed $payload
     * @param  bool $halt
     *
     * @return mixed[]|null
     */
    public function dispatch($event, $payload = null, ?bool $halt = null): ?array;

    /**
     * Configure listener for given events.
     *
     * @param string[] $events
     * @param string $listener
     *
     * @return void
     */
    public function listen(array $events, string $listener): void;
}
