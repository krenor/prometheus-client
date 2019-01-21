<?php

namespace Krenor\Prometheus\Storage\Bindings\Collectors;

use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Builders\CounterSamplesBuilder;

class CounterCollector extends Binding
{
    /**
     * @param Counter $counter
     * @param Collection $items
     *
     * @return CounterSamplesBuilder
     */
    public function __invoke(Counter $counter, Collection $items): CounterSamplesBuilder
    {
        return new CounterSamplesBuilder($counter, $items);
    }
}
