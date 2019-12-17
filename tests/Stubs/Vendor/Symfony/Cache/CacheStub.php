<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Cache;

use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class CacheStub implements CacheInterface
{
    /**
     * @var mixed[]
     */
    private $cacheValues = [];

    /**
     * @var mixed[]
     */
    private $values;

    /**
     * Constructor
     *
     * @param mixed[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return mixed[]
     */
    public function getCacheValues(): array
    {
        return $this->cacheValues;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null)
    {
        $save = true;

        return $this->cacheValues[] = $this->values[$key] ?? $callback(new CacheItem(), $save);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $key): bool
    {
    }
}
