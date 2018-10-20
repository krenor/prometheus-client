<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Builders\CounterSamplesBuilder;

abstract class Counter extends Metric implements Incrementable
{
    /**
     * {@inheritdoc}
     */
    public function builder(Collection $items): SamplesBuilder
    {
        return new CounterSamplesBuilder($this, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return 'counter';
    }

    /**
     * {@inheritdoc}
     */
    public function increment(array $labels = []): self
    {
        return $this->incrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementBy(float $value, array $labels = []): self
    {
        static::$storage->increment($this, $value, $labels);

        return $this;
    }
}
