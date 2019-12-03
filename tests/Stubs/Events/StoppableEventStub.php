<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Events;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @coversNothing
 */
class StoppableEventStub implements StoppableEventInterface
{
    /**
     * Get event name.
     *
     * @return string
     */
    public function getEventName(): string
    {
        return 'event.stoppable_stub';
    }

    /**
     * Get event payload.
     *
     * @return mixed[]
     */
    public function getPayload(): array
    {
        return ['key1' => 'value1'];
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped(): bool
    {
        return true;
    }
}
