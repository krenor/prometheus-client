<?php

namespace Krenor\Prometheus\Metrics;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Exceptions\PrometheusException;
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
     *
     * @return self
     */
    public function increment(array $labels = []): Incrementable
    {
        return $this->incrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function incrementBy(float $value, array $labels = []): Incrementable
    {
        if ($value < 0) {
            throw new PrometheusException('Counters can only be incremented by non-negative amounts.');
        }

        static::$storage->increment($this, $value, $labels);

        return $this;
    }
}
