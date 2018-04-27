<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Settable
{
    /**
     * @param float $value
     * @param array $labels
     *
     * @return Metric
     */
    public function set(float $value, array $labels);
}
