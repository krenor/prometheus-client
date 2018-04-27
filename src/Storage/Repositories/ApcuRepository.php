<?php

namespace Krenor\Prometheus\Storage\Repositories;

use RuntimeException;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;

class ApcuRepository implements Repository
{
    /**
     * {@inheritdoc}
     */
    public function get(string $key): Collection
    {
        return new Collection(apcu_fetch($key) ?: []);
    }

    /**
     * {@inheritdoc}
     */
    public function increment(string $key, string $field, float $value): void
    {
        $collection = $this->get($key);

        $stored = apcu_store($key, $collection->put(
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

        $stored = apcu_store($key, $collection->put($field, $value));

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

        $stored = apcu_store($key, $collection->push($value));

        if (!$stored) {
            throw new RuntimeException('The store method returned false.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        return apcu_clear_cache();
    }
}
