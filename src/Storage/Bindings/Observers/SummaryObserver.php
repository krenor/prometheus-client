<?php

namespace Krenor\Prometheus\Storage\Bindings\Observers;

use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;

class SummaryObserver extends Binding
{
    /**
     * @param Summary $summary
     * @param Collection $labels
     * @param float $value
     *
     * @return void
     */
    public function __invoke(Summary $summary, Collection $labels, float $value): void
    {
        $identifier = "{$this->key}:" . crc32($labels->toJson()) . ':VALUES';

        $this->repository->set($this->key, $labels->toJson(), $identifier, false);
        $this->repository->push($identifier, $value);
    }
}
