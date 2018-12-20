<?php

namespace Krenor\Prometheus\Contracts\Types;

use Krenor\Prometheus\Contracts\Metric;

interface Incrementable extends Metric
{
    /**
     * @param array $labels = []
     *
     * @return self
     */
    public function increment(array $labels = []): self;

    /**
     * @param float $value
     * @param array $labels
     *
     * @return self
     */
    public function incrementBy(float $value, array $labels = []): self;
}
