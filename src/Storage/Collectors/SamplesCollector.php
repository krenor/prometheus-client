<?php

namespace Krenor\Prometheus\Storage\Collectors;

use Closure;
use Krenor\Prometheus\Sample;
use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Storage\Concerns\InteractsWithStoredMetrics;

class SamplesCollector
{
    use InteractsWithStoredMetrics;

    /**
     * @var Metric
     */
    protected $metric;

    /**
     * @var Collection
     */
    protected $stored;

    /**
     * SamplesCollector constructor.
     *
     * @param Metric $metric
     * @param Collection $stored
     */
    public function __construct(Metric $metric, Collection $stored)
    {
        $this->metric = $metric;
        $this->stored = $stored;
    }

    /**
     * @return Collection
     */
    public function collect(): Collection
    {
        return $this->group()->flatMap(function (Collection $group) {
            return $group->map($this->sample($this->key($this->metric)));
        });
    }

    /**
     * @return Collection
     */
    protected function group(): Collection
    {
        $labels = $this->metric->labels()->toArray();

        return $this->stored->map(function ($value, string $key) {
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
    protected function sample(string $name): Closure
    {
        return function (array $item) use ($name) {
            $value = $item['value'];
            $labels = new Collection($item['labels']);

            return new Sample($name, $value, $labels);
        };
    }
}
