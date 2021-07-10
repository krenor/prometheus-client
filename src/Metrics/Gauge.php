<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Settable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Metrics\Concerns\TracksExecutionTime;

abstract class Gauge extends Metric implements Incrementable, Decrementable, Settable
{
    use TracksExecutionTime;

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
    public function increment(array $labels = []): static
    {
        return $this->incrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementBy(float $value, array $labels = []): static
    {
        static::$storage->increment($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(array $labels = []): static
    {
        return $this->decrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function decrementBy(float $value, array $labels = []): static
    {
        static::$storage->decrement($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set(float $value, array $labels = []): static
    {
        static::$storage->set($this, $value, $labels);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function track(float $value, array $labels = []): void
    {
        $this->set($value, $labels);
    }
}
