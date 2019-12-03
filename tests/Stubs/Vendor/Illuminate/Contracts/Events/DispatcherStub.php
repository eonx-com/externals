<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Events;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * {@coversNothing}
 */
final class DispatcherStub implements Dispatcher
{
    /**
     * Calls made.
     *
     * @var mixed[]
     */
    private $calls = [];

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag) Inherited from Laravel dispatcher interface
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        $this->saveCalls(__FUNCTION__, \compact('event', 'payload', 'halt'));

        return $halt  === true ? null : \compact('event', 'payload');
    }

    /**
     * {@inheritdoc}
     */
    public function flush($event)
    {
        $this->saveCalls(__FUNCTION__, \compact('event'));
    }

    /**
     * {@inheritdoc}
     */
    public function forget($event)
    {
        $this->saveCalls(__FUNCTION__, \compact('event'));
    }

    /**
     * {@inheritdoc}
     */
    public function forgetPushed()
    {
        $this->saveCalls(__FUNCTION__, []);
    }

    /**
     * Get call made by method name.
     *
     * @param string $method The method that was called.
     *
     * @return mixed[]
     */
    public function getCall(string $method): array
    {
        return \array_key_exists($method, $this->calls) === true
            ? $this->calls[$method]
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName)
    {
        $this->saveCalls(__FUNCTION__, \compact('eventName'));
    }

    /**
     * {@inheritdoc}
     */
    public function listen($events, $listener)
    {
        $this->saveCalls(__FUNCTION__, \compact('events', 'listener'));
    }

    /**
     * {@inheritdoc}
     */
    public function push($event, $payload = [])
    {
        $this->saveCalls(__FUNCTION__, \compact('event', 'payload'));
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe($subscriber)
    {
        $this->saveCalls(__FUNCTION__, \compact('subscriber'));
    }

    /**
     * {@inheritdoc}
     */
    public function until($event, $payload = [])
    {
        $this->saveCalls(__FUNCTION__, \compact('event', 'payload'));
    }

    /**
     * Save all calls made to this method.
     *
     * @param string $method The method name to save data against.
     * @param mixed[] $args A key/value array of parameter names and their values.
     *
     * @return void
     */
    private function saveCalls(string $method, array $args): void
    {
        $this->calls[$method][] = $args;
    }
}
