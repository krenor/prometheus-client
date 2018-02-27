<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Observable
{
    /**
     * @param float $value
     *
     * @return Metric
     */
    public function observe(float $value);
}
