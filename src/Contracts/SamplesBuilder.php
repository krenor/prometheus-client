<?php

namespace Krenor\Prometheus\Contracts;

use Closure;
use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;

abstract class SamplesBuilder
{
    /**
     * @var Metric
     */
    protected $metric;

    /**
     * @var Collection
     */
    protected $items;

    /**
     * SamplesCollector constructor.
     *
     * @param Metric $metric
     * @param Collection $items
     */
    public function __construct(Metric $metric, Collection $items)
    {
        $this->metric = $metric;
        $this->items = $items;
    }

    /**
     * @return Collection
     */
    public function samples(): Collection
    {
        return $this->group()->flatMap(function (Collection $group) {
            return $group->map($this->build($this->metric->key()));
        });
    }

    /**
     * @return mixed
     */
    abstract protected function initialize();

    /**
     * @return Collection
     */
    protected function group(): Collection
    {
        if ($this->metric->labels()->isEmpty() && $this->items->isEmpty()) {
            return new Collection([
                new Collection([[
                    'labels' => null,
                    'value'  => $this->initialize(),
                ]]),
            ]);
        }

        $labels = $this->metric->labels()->toArray();

        return $this->items
            ->map(function ($value, string $key) {
                return json_decode($key, true) + compact('value');
            })->reject(function (array $data) use ($labels) {
                return !array_key_exists('labels', $data)
                    ?: array_keys($data['labels']) !== $labels;
            })->mapToGroups(function (array $item) {
                return [
                    json_encode($item['labels']) => $item,
                ];
            })->sortKeys();
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function build(string $name): Closure
    {
        return function (array $item) use ($name) {
            $value = $item['value'];
            $labels = new Collection($item['labels']);

            return new Sample($name, $value, $labels);
        };
    }
}
