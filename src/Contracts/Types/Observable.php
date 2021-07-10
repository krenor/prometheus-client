<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Observable extends Metric
{
    /**
     * @param float $value
     * @param array $labels
     *
     * @return static
     */
    public function observe(float $value, array $labels = []): static;
}
