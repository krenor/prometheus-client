<?php

namespace Krenor\Prometheus\Storage\Formatter;

use Closure;
use Krenor\Prometheus\Sample;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesCollector;

class HistogramSamplesCollector extends SamplesCollector
{
    /**
     * @var Histogram
     */
    protected $metric;

    /**
     * {@inheritdoc}
     */
    protected function group(): Collection
    {
        $buckets = (new Collection($this->metric->buckets()))
            ->push(PHP_INT_MAX); // Serving as "+Inf" bucket.

        return parent::group()->map(function (Collection $stored) use ($buckets) {
            $labels = $stored->first()['labels']; // TODO: What if it's missing? ..Can this even happen? 🤔

            // The stored bucket containing "+Inf" has to be excluded because it's handled separately.
            list($filled, $inf) = $stored->partition(function (array $data) {
                return $data['bucket'] !== '+Inf';
            });

            /** @var Collection $filled */
            /** @var Collection $inf */
            $missing = $buckets
                ->diff($stored->pluck('bucket'))
                ->map($this->fill($filled, $inf->first()));

            return $filled
                ->merge($missing)
                ->map(function (array $item) use ($labels) {
                    $item['labels'] = $labels;

                    return $item;
                })->sort($this->sort())
                ->values();
        });
    }

    /**
     * @param Collection $filled
     * @param array|null $inf
     *
     * @return Closure
     */
    protected function fill(Collection $filled, ?array $inf = null): Closure
    {
        return function (float $bucket) use ($filled, $inf) {
            // Use the value of the previous bucket or default to 0.
            $value = $filled->where('bucket', '<', $bucket)->last()['value'] ?? 0;

            // If the "+Inf" bucket is stored use its value instead of using
            // the previous bucket's value for the "pseudo" +Inf bucket.
            if ($bucket === (float) PHP_INT_MAX) {
                $bucket = '+Inf';

                if ($inf) {
                    $value = $inf['value'];
                }
            }

            return compact('bucket', 'value');
        };
    }

    /**
     * @return Closure
     */
    protected function sort(): Closure
    {
        return function (array $left, array $right) {
            // Due to http://php.net/manual/en/language.types.string.php#language.types.string.conversion the
            // bucket containing "+Inf" will be cast to 0. Sorting regularly would end up with it incorrectly
            // sitting at the very first spot instead of where it belongs - at the end.
            if ($left['bucket'] === '+Inf') {
                return 1;
            }

            if ($right['bucket'] === '+Inf') {
                return -1;
            }

            return $left['bucket'] <=> $right['bucket'];
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function sample(string $name): Closure
    {
        return function (array $item) use ($name) {
            $value = $item['value'];
            $labels = new Collection($item['labels']);

            // TODO: _count and _sum, too!
            return new Sample("{$name}_bucket", $value, $labels->put('le', $item['bucket']));
        };
    }
}
