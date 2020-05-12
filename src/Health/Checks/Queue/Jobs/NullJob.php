<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks\Queue\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @codeCoverageIgnore This NullJob is used only as a test during extended health check.
 *
 * @internal This is used internally to test a job dispatched.
 */
final class NullJob implements ShouldQueue
{
    /**
     * Do nothing.
     *
     * @return void
     */
    public function handle(): void
    {
    }
}
