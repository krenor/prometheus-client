<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Types\Incrementable;

abstract class Counter extends Metric implements Incrementable
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
}
