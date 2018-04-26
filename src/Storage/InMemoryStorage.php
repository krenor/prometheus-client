<?php

namespace Krenor\Prometheus\Storage;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Storage;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;
use Krenor\Prometheus\Storage\Concerns\InteractsWithStoredMetrics;

class InMemoryStorage implements Storage
{
    use InteractsWithStoredMetrics, StoresMetrics;

    /**
     * @var Collection
     */
    protected $items;

    /**
     * InMemoryStorage constructor.
     */
    public function __construct()
    {
        $this->items = new Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Metric $metric): Collection
    {
        $key = $this->prefixed($this->key($metric));
        $items = $this->items->get($key);

        switch (true) {
            case $metric instanceof Histogram:
                return $this->samples($metric, $items->merge($this->items->get("{$key}:SUM")));
            case $metric instanceof Summary:
                return $this->samples($metric, $items->map(function (string $key) {
                    return $this->items->get($key);
                }));
            default:
                return $this->samples($metric, $items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels): void
    {
        $key = $this->prefixed($this->key($metric));
        $field = $this->labeled($metric, $labels)->toJson();

        /** @var Collection $collection */
        $collection = $this->items->get($key, new Collection);
        $collection->put($field, $collection->get($field, 0) + $value);

        $this->items->put($key, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(Decrementable $metric, float $value, array $labels): void
    {
        $this->increment($metric, -abs($value), $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function observe(Observable $metric, float $value, array $labels): void
    {
        $key = $this->prefixed($this->key($metric));
        $labeled = $this->labeled($metric, $labels);

        /** @var Collection $collection */
        $collection = $this->items->get($key, new Collection);

        if ($metric instanceof Histogram) {
            $bucketed = $labeled->merge($this->bucket($metric, $value));

            $collection->put(
                $bucketed->toJson(),
                $collection->get($bucketed->toJson(), 0) + 1
            );

            /** @var Collection $sums */
            $sums = $this->items->get("{$key}:SUM", new Collection);

            $this->items->put("{$key}:SUM", $sums->put(
                $labeled->toJson(),
                $sums->get($labeled->toJson(), 0) + $value)
            );
        }

        if ($metric instanceof Summary) {
            $identifier = "$key:" . crc32($labeled->toJson()) . ':VALUES';

            $collection->put($labeled->toJson(), $identifier);

            /** @var Collection $list */
            $list = $this->items->get($identifier, new Collection);

            $this->items->put($identifier, $list->push($value));
        }

        $this->items->put($key, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        $key = $this->prefixed($this->key($gauge));

        $this->items->put($key,
            $this->items
                ->get($key, new Collection)
                ->put($this->labeled($gauge, $labels)->toJson(), $value)
        );
    }
}
