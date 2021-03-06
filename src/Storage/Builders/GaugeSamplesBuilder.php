<?php

namespace Krenor\Prometheus\Storage\Builders;

use Krenor\Prometheus\Metrics\Gauge;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class GaugeSamplesBuilder extends SamplesBuilder
{
    /**
     * GaugeSamplesBuilder constructor.
     *
     * @param Gauge $gauge
     * @param Collection $items
     */
    public function __construct(Gauge $gauge, Collection $items)
    {
        parent::__construct($gauge, $items);
    }
}
