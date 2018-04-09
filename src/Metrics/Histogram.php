<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Types\Observable;

abstract class Histogram extends Metric implements Observable
{
    /**
     * @var int[]
     */
    protected $buckets = [
        .005,
        .01,
        .025,
        .05,
        .1,
        .25,
        .5,
        1,
        2.5,
        5,
        10,
    ];

    /**
     * {@inheritdoc}
     */
    public function observe(float $value, array $labels): self
    {
        $this->registry->storage()->observe($this, $value, $labels);

        return $this;
    }

    /**
     * @return int[]
     */
    public function buckets(): array
    {
        return $this->buckets;
    }
}
