<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Decrementable extends Metric
{
    /**
     * @param array $labels
     *
     * @return static
     */
    public function decrement(array $labels = []): static;

    /**
     * @param float $value
     * @param array $labels
     *
     * @return static
     */
    public function decrementBy(float $value, array $labels = []): static;
}
