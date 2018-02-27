<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Incrementable
{
    /**
     * @return Metric
     */
    public function increment();

    /**
     * @param float $value
     *
     * @return Metric
     */
    public function incrementBy(float $value);
}
