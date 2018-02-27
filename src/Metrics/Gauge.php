<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Metric;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

class Gauge extends Metric implements Incrementable, Decrementable
{
    public function increment()
    {
        return $this->incrementBy(1);
    }

    public function incrementBy(float $value)
    {
        // TODO: Implement incrementBy() method.
    }

    public function decrement()
    {
        return $this->decrementBy(1);
    }

    public function decrementBy(float $value)
    {
        // TODO: Implement decrementBy() method.
    }

    public function set(float $value)
    {
        // TODO: Implement set() method.
    }
}
