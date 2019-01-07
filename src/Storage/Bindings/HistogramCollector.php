<?php

namespace Krenor\Prometheus\Storage\Bindings;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Bindings\Collector;
use Krenor\Prometheus\Storage\Builders\HistogramSamplesBuilder;

class HistogramCollector extends Collector
{
    /**
     * @param Histogram|Metric $histogram
     * @param Collection $items
     *
     * @return HistogramSamplesBuilder|SamplesBuilder
     */
    public function collect(Metric $histogram, Collection $items): SamplesBuilder
    {
        return new HistogramSamplesBuilder(
            $histogram,
            $items->merge($this->repository->get("{$this->key}:SUM"))
        );
    }
}
