<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Contracts\Types\Decrementable;

abstract class Gauge extends Metric implements Incrementable, Decrementable
{
    /**
     * {@inheritdoc}
     */
    final public function type(): string
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
     * @param float $value
     *
     * @throws \Krenor\Prometheus\Exceptions\LabelException
     * @throws \Krenor\Prometheus\Exceptions\StorageException
     * @return self
     */
    public function set(float $value): self
    {
        static::$storage->set($this, $value);

        return $this;
    }
}
