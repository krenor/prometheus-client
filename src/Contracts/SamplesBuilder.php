<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;

abstract class SamplesBuilder
{
    /**
     * @var Metric
     */
    protected Metric $metric;

    protected Collection $items;

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
     * @return Collection|Sample[]
     */
    public function samples(): Collection
    {
        return $this
            ->parse()
            ->map(function (array $data) {
                return new Sample(
                    $data['name'],
                    $data['value'],
                    new Collection($data['labels'])
                );
            });
    }

    /**
     * @return Collection
     */
    protected function parse(): Collection
    {
        $name = $this->metric->key();
        $labels = $this->metric->labels()->toArray();

        if (empty($labels) && $this->items->isEmpty()) {
            return (new Collection)
                ->push(['value' => 0] + compact('name', 'labels'));
        }

        return $this
            ->items
            ->map(function ($value, string $field) use ($name) {
                // Merge stored fields with the value and name to an array.
                return compact('name', 'value') + json_decode($field, true);
            })->reject(function (array $data) use ($labels) {
                // Filter out items lacking the key "labels" or where labels names don't match.
                return !array_key_exists('labels', $data)
                    ?: array_keys($data['labels']) !== $labels;
            })->sortBy('labels')
            ->values();
    }
}
