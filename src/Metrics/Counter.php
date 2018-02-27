<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Metric;
use Krenor\Prometheus\Contracts\Types\Incrementable;

class Counter extends Metric implements Incrementable
{
    public function increment()
    {
        return $this->incrementBy(1);
    }

    public function incrementBy(float $value)
    {
        // TODO: Implement incrementBy() method.
    }
}
