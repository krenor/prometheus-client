<?php

namespace Krenor\Prometheus\Storage\Builders;

use InvalidArgumentException;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class HistogramSamplesBuilder extends SamplesBuilder
{
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
     * {@inheritdoc}
     */
    protected function parse(): Collection
    {
        $name = $this->metric->key();
        $buckets = $this->metric->buckets()->push('+Inf');

        return parent
            ::parse()
            ->groupBy(fn($data) => json_encode($data['labels']))
            ->flatMap(function (Collection $data) use ($name, $buckets) {
                $labels = $data->first()['labels'];

                /** @var $sum Collection */
                /** @var $observations Collection */
                [$sum, $observations] = $data->partition('bucket', null);

                if ($sum->count() !== 1) {
                    throw new InvalidArgumentException('Sum of bucket observations missing.');
                }

                return $this
                    ->pad($this->complete($buckets, $observations), new Collection)
                    ->map(function (array $items) use ($name, $labels) {
                        $items['name'] = "{$name}_bucket";
                        $items['labels'] = $labels + [
                            'le' => $items['bucket'],
                        ];

                        return $items;
                    })->push([
                        'name'   => "{$name}_count",
                        'value'  => $observations->sum('value'),
                        'labels' => $labels,
                    ])->push([
                        'name'   => "{$name}_sum",
                        'value'  => $sum->first()['value'],
                        'labels' => $labels,
                    ]);
            });
    }

    /**
     * @param Collection $buckets
     * @param Collection $observations
     *
     * @return Collection
     */
    private function complete(Collection $buckets, Collection $observations): Collection
    {
        $missing = $buckets
            ->diff($observations->pluck('bucket'))
            ->map(fn($bucket) => compact('bucket') + ['value' => 0]);

        return $observations
            ->reject(fn(array $observation) => !$buckets->contains($observation['bucket']))->merge($missing)
            ->sort(function (array $left, array $right) {
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
            })->values();
    }

    /**
     * @param Collection $data
     * @param Collection $result
     * @param int $sum
     * @param int $i
     *
     * @return Collection
     */
    private function pad(Collection $data, Collection $result, int $sum = 0, int $i = 0): Collection
    {
        if ($i >= $data->count()) {
            return $result;
        }

        $value = $data[$i]['value'] + $sum;
        $bucket = $data[$i]['bucket'];

        $result->push(compact('bucket', 'value'));

        return $this->pad($data, $result, $value, ++$i);
    }
}
