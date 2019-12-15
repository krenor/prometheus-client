<?php

namespace Krenor\Prometheus\Storage\Bindings\Collectors;

use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Builders\SummarySamplesBuilder;

class SummaryCollector extends Binding
{
    /**
     * @param Summary $summary
     * @param Collection $items
     *
     * @return SummarySamplesBuilder
     */
    public function __invoke(Summary $summary, Collection $items): SummarySamplesBuilder
    {
        return new SummarySamplesBuilder(
            $summary,
            $items->map(fn(string $key) => $this->repository->get($key))
        );
    }
}
