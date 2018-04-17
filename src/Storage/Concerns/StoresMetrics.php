<?php

namespace Krenor\Prometheus\Storage\Concerns;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
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
     * @param string $name
     *
     * @return string
     */
    protected function prefixed(string $name)
    {
        return "{$this->prefix}:{$name}";
    }

    /**
     * @param Metric $metric
     * @param array $labels
     *
     * @throws LabelException
     *
     * @return Collection
     */
    protected function labeled(Metric $metric, array $labels): Collection
    {
        $expected = $metric->labels()->count();
        $actual = count($labels);

        if ($expected !== $actual) {
            throw new LabelException("Expected {$expected} label values but only {$actual} were given.");
        }

        return new Collection([
            'labels' => $metric->labels()->combine($labels),
        ]);
    }

    /**
     * @param Histogram $histogram
     * @param float $value
     *
     * @return array
     */
    protected function bucket(Histogram $histogram, float $value): array
    {
        $bucket = $histogram->buckets()->first(function (float $bucket) use ($value) {
            return $value <= $bucket;
        }, '+Inf');

        return compact('bucket');
    }
}
