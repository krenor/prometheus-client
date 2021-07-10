<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Incrementable extends Metric
{
    /**
     * @param array $labels = []
     *
     * @return static
     */
    public function increment(array $labels = []): static;

    /**
     * @param float $value
     * @param array $labels
     *
     * @return static
     */
    public function incrementBy(float $value, array $labels = []): static;
}
