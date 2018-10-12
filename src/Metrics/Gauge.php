<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Types\Settable;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Metrics\Concerns\TracksExecutionTime;
use Krenor\Prometheus\Storage\Builders\GaugeSamplesBuilder;

abstract class Gauge extends Metric implements Incrementable, Decrementable, Settable
{
    use TracksExecutionTime;

    /**
     * {@inheritdoc}
     */
    public function builder(Collection $items): SamplesBuilder
    {
        return new GaugeSamplesBuilder($this, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return 'gauge';
    }

    /**
     * {@inheritdoc}
     */
    public function increment(array $labels): self
    {
        return $this->incrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementBy(float $value, array $labels): self
    {
        static::$storage->increment($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(array $labels): self
    {
        return $this->decrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function decrementBy(float $value, array $labels): self
    {
        static::$storage->decrement($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set(float $value, array $labels): self
    {
        static::$storage->set($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function track(float $value, array $labels): void
    {
        $this->set($value, $labels);
    }
}
