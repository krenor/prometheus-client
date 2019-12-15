<?php

namespace Krenor\Prometheus\Storage\Builders;

use Closure;
use Krenor\Prometheus\Metrics\Summary;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class SummarySamplesBuilder extends SamplesBuilder
{
    /**
     * @var Summary
     */
    protected $metric;

    /**
     * SummarySamplesBuilder constructor.
     *
     * @param Summary $summary
     * @param Collection $items
     */
    public function __construct(Summary $summary, Collection $items)
    {
        parent::__construct($summary, $items);
    }

    /**
     * {@inheritdoc}
     */
    protected function parse(): Collection
    {
        $quantiles = $this->metric->quantiles();

        return parent
            ::parse()
            ->flatMap(function (array $item) use ($quantiles) {
                ['name'   => $name,
                 'labels' => $labels] = $item;

                $values = !$item['value'] instanceof Collection
                    ? new Collection
                    : $item['value'];

                $count = $values->count();

                return $quantiles
                    ->map($this->calculate($values->sort()->values(), $count))
                    ->map(function (array $item) use ($name, $labels) {
                        $item['labels'] = $labels + [
                            'quantile' => $item['quantile']
                        ];

                        return $item + compact('name');
                    })->push([
                        'name'   => "{$name}_count",
                        'value'  => $count,
                        'labels' => $labels,
                    ])->push([
                        'name'   => "{$name}_sum",
                        'value'  => $values->sum(),
                        'labels' => $labels,
                    ]);
            });
    }

    /**
     * @param Collection $values
     * @param int $count
     *
     * @return Closure
     */
    private function calculate(Collection $values, int $count): Closure
    {
        return function (float $quantile) use ($count, $values) {
            $position = $count * $quantile;
            $index = (int) $position;

            $value = floor($position) === $position
                ? ($values->get($index - 1) + $values->get($index)) / 2
                : $values->get($index);

            return compact('quantile', 'value');
        };
    }
}
