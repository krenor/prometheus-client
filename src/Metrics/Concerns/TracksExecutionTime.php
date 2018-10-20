<?php

namespace Krenor\Prometheus\Metrics\Concerns;

use Closure;

trait TracksExecutionTime
{
    /**
     * @param array $labels
     * @param int $precision
     *
     * @return Closure
     */
    public function chronometer($labels = [], int $precision = 4): Closure
    {
        $start = microtime(true);

        return function ($labelz = []) use ($labels, $precision, $start) {
            $delta = microtime(true) - $start;

            $this->track(
                round($delta, $precision),
                array_merge($labels, $labelz)
            );
        };
    }

    /**
     * @param float $value
     * @param array $labels
     *
     * @return void
     */
    abstract protected function track(float $value, array $labels = []): void;
}
