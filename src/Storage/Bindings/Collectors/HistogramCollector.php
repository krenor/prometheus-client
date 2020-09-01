<?php

namespace Krenor\Prometheus\Storage\Bindings\Collectors;

use Krenor\Prometheus\Contracts\Binding;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Builders\HistogramSamplesBuilder;

class HistogramCollector extends Binding
{
    /**
     * @param Histogram $histogram
     * @param Collection $items
     *
     * @return HistogramSamplesBuilder
     */
    public function __invoke(Histogram $histogram, Collection $items): HistogramSamplesBuilder
    {
        return new HistogramSamplesBuilder($histogram, $items->merge(
            $this->repository->get("{$this->key}:SUM")
        ));
    }
}
