<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;

class ApcuRepository extends SimpleRepository
{
    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieve(string $key): mixed
    {
        return apcu_fetch($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function store(string $key, Collection $collection): bool
    {
        return apcu_store($key, $collection);
    }
}
