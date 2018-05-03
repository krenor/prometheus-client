<?php

namespace Krenor\Prometheus\Storage\Repositories;

use Tightenco\Collect\Support\Collection;

class InMemoryRepository extends SimpleRepository
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
    public function flush(): bool
    {
        $this->items = new Collection;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieve(string $key)
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
}
