<?php

namespace Krenor\Prometheus\Storage\Builders;

use Closure;
use Krenor\Prometheus\Sample;
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
     * @return Collection
     */
    protected function initialize(): Collection
    {
        return new Collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function group(): Collection
    {
        return parent::group()->collapse()->map(function ($item) {
            /** @var Collection $values */
            $values = $item['value'];
            $count = $values->count();
            $labels = $item['labels'];

            return $this->metric
                ->quantiles()
                ->map($this->calculate($values->sort()->values(), $count))
                ->push(compact('count'))
                ->push(['sum' => $values->sum()])
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

            return new Sample("{$name}", $item['value'], $labels->put('quantile', $item['quantile']));
        };
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
            $index = $count * $quantile;

            $value = floor($index) === $index
                ? ($values[$index - 1] + $values[$index]) / 2
                : $values[floor($index)];

            return compact('quantile', 'value');
        };
    }
}
