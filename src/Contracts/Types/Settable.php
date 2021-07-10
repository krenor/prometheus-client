<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Settable extends Metric
{
    /**
     * @param float $value
     * @param array $labels
     *
     * @return static
     */
    public function set(float $value, array $labels = []): static;
}
