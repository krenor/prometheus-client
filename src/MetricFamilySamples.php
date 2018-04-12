<?php

namespace Krenor\Prometheus;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;

class MetricFamilySamples
{
    /**
     * @var Metric
     */
    protected $metric;

    /**
     * @var Collection
     */
    protected $samples;

    /**
     * MetricFamilySamples constructor.
     *
     * @param Metric $metric
     * @param Collection $samples
     */
    public function __construct(Metric $metric, Collection $samples)
    {
        $this->metric = $metric;
        $this->samples = $samples;
    }

    /**
     * @return Metric
     */
    public function metric(): Metric
    {
        return $this->metric;
    }

    /**
     * @return Collection
     */
    public function samples(): Collection
    {
        return $this->samples;
    }
}
