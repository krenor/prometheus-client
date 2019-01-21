<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Decrementable extends Metric
{
    /**
     * @param array $labels
     *
     * @return self
     */
    public function decrement(array $labels = []): self;

    /**
     * @param float $value
     * @param array $labels
     *
     * @return self
     */
    public function decrementBy(float $value, array $labels = []): self;
}
