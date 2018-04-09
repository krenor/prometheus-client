<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Types\Observable;

abstract class Summary extends Metric implements Observable
{
    /**
     * @var int[]
     */
    protected $quantile = [
        .01,
        .05,
        .5,
        .9,
        .99,
    ];

    /**
     * {@inheritdoc}
     */
    public function observe(float $value, array $labels): self
    {
        return $this->registry->storage()->observe($this, $value, $labels);
    }

    /**
     * @return int[]
     */
    public function quantile(): array
    {
        return $this->quantile;
    }
}
