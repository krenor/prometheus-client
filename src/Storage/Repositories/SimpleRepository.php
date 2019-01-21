<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Exceptions\StorageException;

abstract class SimpleRepository implements Repository
{
    /**
     * {@inheritdoc}
     */
    public function get(string $key): Collection
    {
        return new Collection($this->retrieve($key) ?: []);
    }

    /**
     * {@inheritdoc}
     */
    public function increment(string $key, string $field, float $value): void
    {
        $collection = $this->get($key);

        $collection->put($field,
            $collection->get($field, 0) + $value
        );

        if (!$this->store($key, $collection)) {
            throw new StorageException('The storage returned `false`.');
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
    public function set(string $key, string $field, $value): void
    {
        $collection = $this->get($key);

        if (!$this->store($key, $collection->put($field, $value))) {
            throw new StorageException('The storage returned `false`.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $key, float $value): void
    {
        $collection = $this->get($key);

        if (!$this->store($key, $collection->push($value))) {
            throw new StorageException('The storage returned `false`.');
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function retrieve(string $key);

    /**
     * @param string $key
     * @param Collection $collection
     *
     * @return bool
     */
    abstract protected function store(string $key, Collection $collection): bool;
}
