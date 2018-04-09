<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Incrementable
{
    /**
     * @param array $values
     *
     * @return Metric
     */
    public function increment(array $values);

    /**
     * @param float $value
     * @param array $labels
     *
     * @return Metric
     */
    public function incrementBy(float $value, array $labels);
}
