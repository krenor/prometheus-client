<?php

namespace Krenor\Prometheus;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;

class MetricFamilySamples
{
    /**
     * MetricFamilySamples constructor.
     *
     * @param Metric $metric
     * @param Collection|Sample[] $samples
     */
    public function __construct(protected Metric $metric, protected Collection $samples)
    {
        //
    }

    /**
     * @return Metric
     */
    public function metric(): Metric
    {
        return $this->metric;
    }

    /**
     * @return Collection|Sample[]
     */
    public function samples(): Collection
    {
        return $this->samples;
    }
}
