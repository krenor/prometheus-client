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
        $buckets = $this->metric->buckets()->push('+Inf');

        return parent::group()->map(function (Collection $stored) use ($buckets) {
            $sum = $stored->pop()['value'];
            $labels = $stored->first()['labels']; // TODO: What if it's missing? ..Can this even happen? 🤔

            $missing = $buckets
                ->diff($stored->pluck('bucket'))
                ->map(function ($bucket) {
                    return compact('bucket') + ['value' => 0];
                });

            $merged = $stored
                ->merge($missing)
                ->sort($this->sort())
                ->values(); // Drop the sorted indices.

            return $this
                ->fill($merged, new Collection)
                ->push(['count' => $stored->sum('value')])
                ->push(compact('sum'))
                ->map(function ($item) use ($labels) {
                    $item['labels'] = $labels;

                    return $item;
                });
        });
    }

    /**
     * @param Collection $original
     * @param Collection $result
     * @param int $sum
     * @param int $i
     *
     * @return Collection
     */
    protected function fill(Collection $original, Collection $result, int $sum = 0, int $i = 0): Collection
    {
        if ($i >= $original->count()) {
            return $result;
        }

        $value = $original[$i]['value'] + $sum;
        $bucket = $original[$i]['bucket'];

        $result->push(compact('bucket', 'value'));

        return $this->fill($original, $result, $value, ++$i);
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
            $labels = new Collection($item['labels']);

            if (array_key_exists('count', $item)) {
                return new Sample("{$name}_count", $item['count'], $labels);
            }

            if (array_key_exists('sum', $item)) {
                return new Sample("{$name}_sum", $item['sum'], $labels);
            }

            return new Sample("{$name}_bucket", $item['value'], $labels->put('le', $item['bucket']));
        };
    }
}
