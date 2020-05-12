<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Redis;

use Illuminate\Contracts\Redis\Connector;

/**
 * @coversNothing
 */
class RedisClientStub implements Connector
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $config, array $options)
    {
        return new RedisConnectionStub();
    }

    /**
     * {@inheritdoc}
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options)
    {
        return new RedisConnectionStub();
    }
}
