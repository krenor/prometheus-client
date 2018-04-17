<?php

namespace Krenor\Prometheus\Contracts;

use Closure;
use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;

class SamplesCollector
{
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
        $name = "{$this->metric->namespace()}:{$this->metric->name()}";

        return $this->group()->flatMap(function (Collection $group) use ($name) {
            return $group->map($this->sample($name));
        });
    }

    /**
     * @return Collection
     */
    protected function group(): Collection
    {
        return $this->stored->mapToGroups(function (string $value, string $key) {
            $raw = json_decode($key, true);

            return [
                json_encode($raw['labels']) => compact('value') + $raw,
            ];
        });
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
