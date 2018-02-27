<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Decrementable
{
    /**
     * @return Metric
     */
    public function decrement();

    /**
     * @param float $value
     *
     * @return Metric
     */
    public function decrementBy(float $value);
}
