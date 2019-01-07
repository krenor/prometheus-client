<?php

namespace Krenor\Prometheus\Storage\Bindings;

use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Bindings\Collector;
use Krenor\Prometheus\Storage\Builders\SummarySamplesBuilder;

class SummaryCollector extends Collector
{
    /**
     * @param Summary|Metric $summary
     * @param Collection $items
     *
     * @return SummarySamplesBuilder|SamplesBuilder
     */
    public function collect(Metric $summary, Collection $items): SamplesBuilder
    {
        return new SummarySamplesBuilder(
            $summary,
            $items->map(function (string $key) {
                return $this->repository->get($key);
            })
        );
    }
}
