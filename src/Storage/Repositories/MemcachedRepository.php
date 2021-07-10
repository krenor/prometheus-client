<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Memcached;
use Tightenco\Collect\Support\Collection;

class MemcachedRepository extends SimpleRepository
{
    /**
     * MemcachedRepository constructor.
     *
     * @param Memcached $memcached
     */
    public function __construct(protected Memcached $memcached)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieve(string $key): mixed
    {
        return $this->memcached->get($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function store(string $key, Collection $collection): bool
    {
        return $this->memcached->set($key, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        return $this->memcached->flush();
    }
}
