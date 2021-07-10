<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;

abstract class Exporter
{
    use StoresMetrics;

    /**
     * Exporter constructor.
     *
     * @param array $data
     */
    public function __construct(protected array $data)
    {
        //
    }

    /**
     * @param Metric $metric
     * @param float $value
     * @param array $labels
     *
     * @return MetricFamilySamples
     */
    protected function sampled(Metric $metric, float $value, array $labels = []): MetricFamilySamples
    {
        return new MetricFamilySamples($metric, new Collection([
            new Sample(
                $metric->key(),
                $value,
                $this->labeled($metric, $labels)->get('labels')
            ),
        ]));
    }
}
