<?php

namespace Krenor\Prometheus\Storage\Builders;

use Closure;
use InvalidArgumentException;
use Krenor\Prometheus\Sample;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class HistogramSamplesBuilder extends SamplesBuilder
{
    /**
     * @var Histogram
     */
    protected $metric;

    /**
     * HistogramSamplesBuilder constructor.
     *
     * @param Histogram $histogram
     * @param Collection $items
     */
    public function __construct(Histogram $histogram, Collection $items)
    {
        parent::__construct($histogram, $items);
    }

    /**
     * @return int
     */
    protected function initialize(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function group(): Collection
    {
        $buckets = $this->metric->buckets()->push('+Inf');

        return parent::group()->map(function (Collection $items) use ($buckets) {
            $sum = $items->pop();

            if (array_key_exists('bucket', $sum)) {
                throw new InvalidArgumentException('The last element has to be the sum of all bucket observations.');
            }

            $labels = $items->first()['labels'];

            return $this
                ->fill($this->all($buckets, $items), new Collection)
                ->push(['count' => $items->sum('value')])
                ->push(['sum' => $sum['value']])
                ->map(function ($item) use ($labels) {
                    $item['labels'] = $labels;

                    return $item;
                });
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function build(string $name): Closure
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

    /**
     * @param Collection $buckets
     * @param Collection $items
     *
     * @return Collection
     */
    private function all(Collection $buckets, Collection $items): Collection
    {
        $missing = $buckets
            ->diff($items->pluck('bucket'))
            ->map(function ($bucket) {
                return compact('bucket') + ['value' => 0];
            });

        return $items
            ->reject(function (array $item) use ($buckets) {
                return !$buckets->contains($item['bucket']);
            })->merge($missing)
            ->sort($this->sort())
            ->values();
    }

    /**
     * @return Closure
     */
    private function sort(): Closure
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
     * @param Collection $items
     * @param Collection $result
     * @param int $sum
     * @param int $i
     *
     * @return Collection
     */
    private function fill(Collection $items, Collection $result, int $sum = 0, int $i = 0): Collection
    {
        if ($i >= $items->count()) {
            return $result;
        }

        $value = $items[$i]['value'] + $sum;
        $bucket = $items[$i]['bucket'];

        $result->push(compact('bucket', 'value'));

        return $this->fill($items, $result, $value, ++$i);
    }
}
