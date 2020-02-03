<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Redis;

use Closure;
use Illuminate\Contracts\Redis\Connection as ConnectionContract;
use Illuminate\Redis\Connections\Connection;

/**
 * @coversNothing
 */
class RedisConnectionStub extends Connection implements ConnectionContract
{
    /**
     * {@inheritdoc}
     */
    public function createSubscription($channels, Closure $callback, $method = 'subscribe')
    {
    }
}
