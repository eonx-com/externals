<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface as PsrStoppableEventInterface;

final class EventDispatcher implements PsrEventDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $dispatcher;

    /**
     * Create event dispatcher.
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
    public function dispatch(object $event)
    {
        $halt = false;

        // According to PSR-14, any halt-able event should implement StoppableEventInterface and must
        // return true from isPropagationStopped() when the event is completed.
        // @see https://www.php-fig.org/psr/psr-14/
        if (($event instanceof PsrStoppableEventInterface) === true) {
            $halt = $event->isPropagationStopped();
        }

        $this->dispatcher->dispatch($event, [], $halt);

        return $event;
    }
}
