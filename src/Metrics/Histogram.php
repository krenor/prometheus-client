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
    final public function type(): string
    {
        return 'histogram';
    }

    /**
     * {@inheritdoc}
     */
    public function observe(float $value, array $labels): self
    {
        static::$storage->observe($this, $value, $labels);

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
