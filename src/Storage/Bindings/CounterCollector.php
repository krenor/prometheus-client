<?php

namespace Krenor\Prometheus\Storage\Bindings;

use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Bindings\Collector;
use Krenor\Prometheus\Storage\Builders\CounterSamplesBuilder;

class CounterCollector extends Collector
{
    /**
     * @param Counter|Metric $counter
     * @param Collection $items
     *
     * @return CounterSamplesBuilder|SamplesBuilder
     */
    public function collect(Metric $counter, Collection $items): SamplesBuilder
    {
        return new CounterSamplesBuilder($counter, $items);
    }
}
