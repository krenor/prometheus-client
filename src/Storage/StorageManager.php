<?php

namespace Krenor\Prometheus\Storage;

use Exception;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Storage;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Collectors\SamplesCollector;
use Krenor\Prometheus\Storage\Collectors\SummarySamplesCollector;
use Krenor\Prometheus\Storage\Collectors\HistogramSamplesCollector;

class StorageManager implements Storage
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * StorageManager constructor.
     *
     * @param Repository $repository
     * @param string|null $prefix
     */
    public function __construct(Repository $repository, ?string $prefix = 'PROMETHEUS')
    {
        $this->repository = $repository;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Metric $metric): Collection
    {
        $key = "{$this->prefix}:{$metric->key()}";

        try {
            $items = $this->repository->get($key);

            switch (true) {
                case $metric instanceof Histogram:
                    return $this->samples($metric, $items->merge($this->repository->get("{$key}:SUM")));
                case $metric instanceof Summary:
                    return $this->samples($metric, $items->map(function (string $key) {
                        return $this->repository->get($key);
                    }));
                default:
                    return $this->samples($metric, $items);
            }
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to collect the samples of [$class]: {$e}", 0, $e);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels): void
    {
        try {
            $this->repository->increment(
                "{$this->prefix}:{$metric->key()}",
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to increment [$class] by `$value`: {$e}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(Decrementable $metric, float $value, array $labels): void
    {
        try {
            $this->repository->decrement(
                "{$this->prefix}:{$metric->key()}",
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to decrement [$class] by `$value`: {$e}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function observe(Observable $metric, float $value, array $labels): void
    {
        $key = "{$this->prefix}:{$metric->key()}";
        $labeled = $this->labeled($metric, $labels);
        $field = $labeled->toJson();

        try {
            if ($metric instanceof Histogram) {
                $this->repository->increment($key, $labeled->merge($this->bucket($metric, $value))->toJson(), 1);
                $this->repository->increment("{$key}:SUM", $field, $value);
            }

            if ($metric instanceof Summary) {
                $identifier = "$key:" . crc32($field) . ':VALUES';

                $this->repository->set($key, $field, $identifier, false);
                $this->repository->push($identifier, $value);
            }
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to observe [$class] with `$value`: {$e}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        try {
            $this->repository->set(
                "{$this->prefix}:{$gauge->key()}",
                $this->labeled($gauge, $labels)->toJson(),
                $value
            );
        } catch (Exception $e) {
            $class = get_class($gauge);

            throw new StorageException("Failed to set the value of [$class] to `$value`: {$e}", 0, $e);
        }
    }

    /**
     * @param Metric $metric
     * @param Collection $items
     *
     * @return Collection
     */
    protected function samples(Metric $metric, Collection $items): Collection
    {
        switch (true) {
            case $metric instanceof Histogram:
                return (new HistogramSamplesCollector($metric, $items))->collect();
            case $metric instanceof Summary:
                return (new SummarySamplesCollector($metric, $items))->collect();
            default:
                return (new SamplesCollector($metric, $items))->collect();
        }
    }

    /**
     * @param Metric $metric
     * @param array $labels
     *
     * @throws LabelException
     *
     * @return Collection
     */
    protected function labeled(Metric $metric, array $labels): Collection
    {
        $expected = $metric->labels()->count();
        $actual = count($labels);

        if ($expected !== $actual) {
            throw new LabelException("Expected {$expected} label values but only {$actual} were given.");
        }

        return new Collection([
            'labels' => $metric->labels()->combine($labels),
        ]);
    }

    /**
     * @param Histogram $histogram
     * @param float $value
     *
     * @return array
     */
    protected function bucket(Histogram $histogram, float $value): array
    {
        $bucket = $histogram->buckets()->first(function (float $bucket) use ($value) {
            return $value <= $bucket;
        }, '+Inf');

        return compact('bucket');
    }
}
