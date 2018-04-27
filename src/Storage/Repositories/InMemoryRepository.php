<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;

class InMemoryRepository implements Repository
{
    /**
     * @var Collection
     */
    protected $items;

    /**
     * InMemoryRepository constructor.
     */
    public function __construct()
    {
        $this->items = new Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): Collection
    {
        return $this->items->get($key, new Collection);
    }

    /**
     * {@inheritdoc}
     */
    public function increment(string $key, string $field, float $value): void
    {
        $collection = $this->get($key);

        $this->items->put($key, $collection->put(
            $field,
            $collection->get($field, 0) + $value
        ));
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

        if (!$override && $this->get($key)->get($field) !== null) {
            return;
        }

        $this->items->put($key,
            $collection->put($field, $value)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $key, float $value): void
    {
        $collection = $this->get($key);

        if ($collection->isEmpty()) {
            $this->items->put($key, $collection);
        }

        $collection->push($value);
    }
}
