<?php

namespace Krenor\Prometheus\Contracts\Bindings;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Contracts\SamplesBuilder;

abstract class Collector
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $key;

    /**
     * Collector constructor.
     *
     * @param Repository $repository
     * @param string $key
     */
    public function __construct(Repository $repository, string $key)
    {
        $this->repository = $repository;
        $this->key = $key;
    }

    /**
     * @param Metric $metric
     * @param Collection $items
     *
     * @return SamplesBuilder
     */
    abstract public function collect(Metric $metric, Collection $items): SamplesBuilder;
}
