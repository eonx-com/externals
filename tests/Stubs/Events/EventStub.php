<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Events;

/**
 * @coversNothing
 */
class EventStub
{
    /**
     * Get event name.
     *
     * @return string
     */
    public function getEventName(): string
    {
        return 'event.stub';
    }

    /**
     * Get event payload.
     *
     * @return mixed[]
     */
    public function getPayload(): array
    {
        return ['key' => 'value'];
    }
}
