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
    public function increment(): self
    {
        return $this->incrementBy(1);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementBy(float $value): self
    {
        $this->registry->storage()->increment($this, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(): self
    {
        return $this->decrementBy(1);
    }

    /**
     * {@inheritdoc}
     */
    public function decrementBy(float $value): self
    {
        $this->registry->storage()->decrement($this, $value);

        return $this;
    }

    /**
     * @param float $value
     *
     * @return self
     */
    public function set(float $value): self
    {
        $this->registry->storage()->set($this, $value);

        return $this;
    }
}
