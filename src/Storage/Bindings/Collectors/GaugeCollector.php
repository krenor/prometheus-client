<?php

namespace Krenor\Prometheus\Storage\Bindings\Collectors;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Builders\GaugeSamplesBuilder;

class GaugeCollector extends Binding
{
    /**
     * @param Gauge $gauge
     * @param Collection $items
     *
     * @return GaugeSamplesBuilder
     */
    public function __invoke(Gauge $gauge, Collection $items): GaugeSamplesBuilder
    {
        return new GaugeSamplesBuilder($gauge, $items);
    }
}
