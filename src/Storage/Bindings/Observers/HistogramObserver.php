<?php

namespace Krenor\Prometheus\Storage\Bindings\Observers;

use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;

class HistogramObserver extends Binding
{
    /**
     * @param Histogram $histogram
     * @param Collection $labels
     * @param float $value
     *
     * @return void
     */
    public function __invoke(Histogram $histogram, Collection $labels, float $value): void
    {
        $bucket = $histogram
            ->buckets()
            ->first(function (float $bucket) use ($value) {
                return $value <= $bucket;
            }, '+Inf');

        $this->repository->increment($this->key, $labels->merge(compact('bucket'))->toJson(), 1);
        $this->repository->increment("{$this->key}:SUM", $labels->toJson(), $value);
    }
}
