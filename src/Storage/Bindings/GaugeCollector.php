<?php

namespace Krenor\Prometheus\Storage\Bindings;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Bindings\Collector;
use Krenor\Prometheus\Storage\Builders\GaugeSamplesBuilder;

class GaugeCollector extends Collector
{
    /**
     * @param Gauge|Metric $gauge
     * @param Collection $items
     *
     * @return GaugeSamplesBuilder|SamplesBuilder
     */
    public function collect(Metric $gauge, Collection $items): SamplesBuilder
    {
        return new GaugeSamplesBuilder($gauge, $items);
    }
}
