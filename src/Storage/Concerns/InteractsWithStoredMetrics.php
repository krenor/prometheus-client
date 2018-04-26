<?php

namespace Krenor\Prometheus\Storage\Concerns;

use Krenor\Prometheus\Contracts\Metric;

trait InteractsWithStoredMetrics
{
    /**
     * @param Metric $metric
     *
     * @return string
     */
    protected function key(Metric $metric): string
    {
        // TODO: Why again did I remove this from the Metric class and put it into a trait?
        return "{$metric->namespace()}_{$metric->name()}";
    }
}
