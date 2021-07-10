<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Exceptions\PrometheusException;

abstract class Counter extends Metric implements Incrementable
{
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
    public function increment(array $labels = []): static
    {
        return $this->incrementBy(1, $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementBy(float $value, array $labels = []): static
    {
        if ($value < 0) {
            throw new PrometheusException('Counters can only be incremented by non-negative amounts.');
        }

        static::$storage->increment($this, $value, $labels);

        return $this;
    }
}
