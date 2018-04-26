<?php

namespace Krenor\Prometheus\Storage\Concerns;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\LabelException;

trait StoresMetrics
{
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
