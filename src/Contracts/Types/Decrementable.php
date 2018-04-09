<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Decrementable
{
    /**
     * @param array $values
     *
     * @return Metric
     */
    public function decrement(array $values);

    /**
     * @param float $value
     * @param array $labels
     *
     * @return Metric
     */
    public function decrementBy(float $value, array $labels);
}
