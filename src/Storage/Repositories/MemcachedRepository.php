<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Memcached;
use Tightenco\Collect\Support\Collection;

class MemcachedRepository extends SimpleRepository
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
    public function flush(): bool
    {
        return $this->memcached->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieve(string $key)
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
}
