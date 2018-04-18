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
