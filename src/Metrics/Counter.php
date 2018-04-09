<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Types\Incrementable;

abstract class Counter extends Metric implements Incrementable
{
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
        $this->registry->storage()->increment($this, $value, $labels);

        return $this;
    }
}
