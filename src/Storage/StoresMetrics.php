<?php

namespace Krenor\Prometheus\Storage;

use Tightenco\Collect\Support\Arr;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Exceptions\LabelException;

trait StoresMetrics
{
    /**
     * @var string
     */
    protected $prefix = 'PROMETHEUS';

    /**
     * @param string $prefix
     *
     * @return self
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param Metric $metric
     *
     * @return string
     */
    protected function key(Metric $metric): string
    {
        return "{$this->prefix}:{$metric->namespace()}:{$metric->name()}";
    }

    /**
     * @param Metric $metric
     * @param array labels
     * @param float|null $value
     *
     * @throws LabelException
     *
     * @return string
     */
    protected function field(Metric $metric, array $labels, ?float $value = null): string
    {
        $combined = array_combine($metric->labels(), $labels);

        if (!$combined) {
            $expected = count($metric->labels());
            $actual = count($labels);

            throw new LabelException("Expected {$expected} label values but only {$actual} were given.");
        }

        $field = ['labels' => $combined];

        if ($metric instanceof Histogram && $value !== null) {
            $field['bucket'] = Arr::first($metric->buckets(), function (float $bucket) use ($value) {
                return $value <= $bucket;
            }, '+Inf');
        }

        return json_encode($field);
    }
}
