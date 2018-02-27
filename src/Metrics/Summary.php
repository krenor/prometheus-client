<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Types\Metric;
use Krenor\Prometheus\Contracts\Types\Observable;

class Summary extends Metric implements Observable
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

    public function observe(float $value)
    {
        // TODO: Implement observe() method.
    }

    /**
     * @return int[]
     */
    public function quantile(): array
    {
        return $this->quantile;
    }

    /**
     * @param int[] $quantile
     *
     * @return Summary
     */
    public function setQuantile(array $quantile): Summary
    {
        $this->quantile = $quantile;

        return $this;
    }
}
