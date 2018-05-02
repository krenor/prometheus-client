<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Memcached;
use RuntimeException;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;

class MemcachedRepository implements Repository
{
    /**
     * @var Memcached
     */
    protected $memcached;

    /**
     * MemcachedRepository constructor.
     *
     * @param Memcached $memcached
     */
    public function __construct(Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): Collection
    {
        return new Collection($this->memcached->get($key) ?: []);
    }

    /**
     * {@inheritdoc}
     */
    public function increment(string $key, string $field, float $value): void
    {
        $collection = $this->get($key);

        $stored = $this->memcached->set($key, $collection->put(
            $field,
            $collection->get($field, 0) + $value
        ));

        if (!$stored) {
            throw new RuntimeException('The store method returned false.');
        }
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
    public function set(string $key, string $field, $value, $override = true): void
    {
        $collection = $this->get($key);

        if (!$override && $collection->get($field) !== null) {
            return;
        }

        $stored = $this->memcached->set($key, $collection->put($field, $value));

        if (!$stored) {
            throw new RuntimeException('The store method returned false.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $key, float $value): void
    {
        $collection = $this->get($key);

        $stored = $this->memcached->set($key, $collection->push($value));

        if (!$stored) {
            throw new RuntimeException('The store method returned false.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        return $this->memcached->flush();
    }
}
