<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface as EoneoeDispatcherInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class PsrEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor
     *
     * @param \EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface $dispatcher
     */
    public function __construct(EoneoeDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event)
    {
        $this->dispatcher->dispatch($event);
    }
}
