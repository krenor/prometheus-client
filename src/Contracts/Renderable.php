<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;

interface Renderable
{
    /**
     * @param Collection|MetricFamilySamples[] $metrics
     *
     * @return string
     */
    public function render(Collection $metrics): string;
}
