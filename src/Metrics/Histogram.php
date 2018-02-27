<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Metric;
use Krenor\Prometheus\Contracts\Types\Observable;

class Histogram extends Metric implements Observable
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

    public function observe(float $value)
    {
        // TODO: Implement observe() method.
    }

    /**
     * @return int[]
     */
    public function buckets(): array
    {
        return $this->buckets;
    }

    /**
     * @param int[] $buckets
     *
     * @return Histogram
     */
    public function setBuckets(array $buckets): Histogram
    {
        $this->buckets = $buckets;

        return $this;
    }
}
