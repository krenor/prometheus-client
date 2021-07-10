<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Storage\Redis\Connection as Redis;

class RedisRepository implements Repository
{
    /**
     * RedisRepository constructor.
     *
     * @param Redis $redis
     */
    public function __construct(protected Redis $redis)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): Collection
    {
        return new Collection(
            !str_contains($key, ':VALUES')
                ? $this->redis->hgetall($key)
                : $this->redis->lrange($key, 0, -1)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function increment(string $key, string $field, float $value): void
    {
        $this->redis->hincrbyfloat($key, $field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(string $key, string $field, float $value): void
    {
        $this->increment($key, $field, -abs($value));
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, string $field, mixed $value): void
    {
        $this->redis->hset($key, $field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $key, float $value): void
    {
        $this->redis->rpush($key, [$value]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        return $this->redis->flushdb();
    }
}
