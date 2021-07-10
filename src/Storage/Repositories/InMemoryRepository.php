<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;

class InMemoryRepository extends SimpleRepository
{
    protected Collection $items;

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
    protected function retrieve(string $key): mixed
    {
        return $this->items->get($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function store(string $key, Collection $collection): bool
    {
        $this->items->put($key, $collection);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): bool
    {
        $this->items = new Collection;

        return true;
    }
}
