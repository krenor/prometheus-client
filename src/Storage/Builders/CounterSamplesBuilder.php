<?php

namespace Krenor\Prometheus\Storage\Builders;

use Krenor\Prometheus\Metrics\Counter;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class CounterSamplesBuilder extends SamplesBuilder
{
    /**
     * CounterSamplesBuilder constructor.
     *
     * @param Counter $counter
     * @param Collection $items
     */
    public function __construct(Counter $counter, Collection $items)
    {
        parent::__construct($counter, $items);
    }
}
