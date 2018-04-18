<?php

namespace Krenor\Prometheus\Storage\Concerns;

use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Collectors\SamplesCollector;
use Krenor\Prometheus\Storage\Collectors\SummarySamplesCollector;
use Krenor\Prometheus\Storage\Collectors\HistogramSamplesCollector;

trait InteractsWithStoredMetrics
{
    /**
     * @param Metric $metric
     *
     * @return string
     */
    protected function key(Metric $metric): string
    {
        return "{$metric->namespace()}_{$metric->name()}";
    }

    /**
     * @param Metric $metric
     * @param Collection $items
     *
     * @return Collection
     */
    protected function samples(Metric $metric, Collection $items): Collection
    {
        switch (true) {
            case $metric instanceof Histogram:
                return (new HistogramSamplesCollector($metric, $items))->collect();
            case $metric instanceof Summary:
                return (new SummarySamplesCollector($metric, $items))->collect();
            default:
                return (new SamplesCollector($metric, $items))->collect();
        }
    }
}
