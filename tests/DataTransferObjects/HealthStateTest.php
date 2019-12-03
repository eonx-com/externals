<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\DataTransferObjects;

use EoneoPay\Externals\DataTransferObjects\Health\HealthState;
use EoneoPay\Externals\Health\Interfaces\HealthInterface;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\DataTransferObjects\Health\HealthState
 */
class HealthStateTest extends TestCase
{
    /**
     * Gets the scenarios to test the default health state messages.
     *
     * @return mixed[]
     */
    public function getMessageScenarios(): iterable
    {
        yield 'Expect default healthy message' => [
            'state' => HealthInterface::STATE_HEALTHY,
            'expected' => HealthState::DEFAULT_HEALTHY_MESSSAGE
        ];

        yield 'Expect default degraded message' => [
            'state' => HealthInterface::STATE_DEGRADED,
            'expected' => HealthState::DEFAULT_DEGRADED_MESSAGE
        ];
    }

    /**
     * Tests the creation of the DTO and ensures that the methods return the same values as those provided
     * in the constructor.
     *
     * @return void
     */
    public function testCreation(): void
    {
        $instance = new HealthState(
            HealthInterface::STATE_HEALTHY,
            'An apple a day keeps service degradation away.'
        );

        self::assertSame(HealthInterface::STATE_HEALTHY, $instance->getState());
        self::assertSame('An apple a day keeps service degradation away.', $instance->getMessage());
    }

    /**
     * Tests that the default message returned by the health state for each state from the data
     * provider matches the expected value.
     *
     * @param int $state The health state.
     * @param string $expected The expected message.
     *
     * @return void
     *
     * @dataProvider getMessageScenarios
     */
    public function testDefaultMessages(int $state, string $expected): void
    {
        $instance = new HealthState($state);

        self::assertSame($expected, $instance->getMessage());
    }
}
