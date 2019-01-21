<?php

namespace Krenor\Prometheus\Storage\Concerns;

use Krenor\Prometheus\Contracts\Metric;
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
            throw new LabelException("Expected {$expected} label values but {$actual} were given.");
        }

        return new Collection([
            'labels' => $metric->labels()->combine($labels),
        ]);
    }
}
