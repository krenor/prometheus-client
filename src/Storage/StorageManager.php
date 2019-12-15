<?php

namespace Krenor\Prometheus\Storage;

use Exception;
use RuntimeException;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Contracts\Storage;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Contracts\SamplesBuilder;
use Krenor\Prometheus\Contracts\Types\Settable;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;
use Krenor\Prometheus\Storage\Bindings\Collectors\GaugeCollector;
use Krenor\Prometheus\Storage\Bindings\Observers\SummaryObserver;
use Krenor\Prometheus\Storage\Bindings\Collectors\CounterCollector;
use Krenor\Prometheus\Storage\Bindings\Collectors\SummaryCollector;
use Krenor\Prometheus\Storage\Bindings\Observers\HistogramObserver;
use Krenor\Prometheus\Storage\Bindings\Collectors\HistogramCollector;

class StorageManager implements Storage
{
    const COLLECTOR_BINDING_KEY = 'collect';
    const OBSERVER_BINDING_KEY = 'observe';

    use StoresMetrics;

    protected Repository $repository;

    protected string $prefix;

    protected array $bindings = [
        self::COLLECTOR_BINDING_KEY => [
            Counter::class   => CounterCollector::class,
            Gauge::class     => GaugeCollector::class,
            Histogram::class => HistogramCollector::class,
            Summary::class   => SummaryCollector::class,
        ],
        self::OBSERVER_BINDING_KEY  => [
            Histogram::class => HistogramObserver::class,
            Summary::class   => SummaryObserver::class,
        ],
    ];

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
        try {
            $items = $this->repository->get("{$this->prefix}:{$metric->key()}");
            $collector = $this->binding(self::COLLECTOR_BINDING_KEY, $metric);

            /** @var SamplesBuilder $builder */
            $builder = $collector($metric, $items);

            if (!$builder instanceof SamplesBuilder) {
                throw new RuntimeException("The collector did not resolve into a SamplesBuilder.");
            }

            return $builder->samples();
        } catch (LabelException $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to collect the samples of [{$class}]: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels = []): void
    {
        try {
            $this->repository->increment(
                "{$this->prefix}:{$metric->key()}",
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (LabelException $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to increment [{$class}] by `{$value}`: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(Decrementable $metric, float $value, array $labels = []): void
    {
        try {
            $this->repository->decrement(
                "{$this->prefix}:{$metric->key()}",
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (LabelException $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to decrement [{$class}] by `{$value}`: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function observe(Observable $metric, float $value, array $labels = []): void
    {
        try {
            $this->binding(self::OBSERVER_BINDING_KEY, $metric)
            ($metric, $this->labeled($metric, $labels), $value);
        } catch (LabelException $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to observe [{$class}] with `{$value}`: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Settable $metric, float $value, array $labels = []): void
    {
        try {
            $this->repository->set(
                "{$this->prefix}:{$metric->key()}",
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (LabelException $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to set [{$class}] to `{$value}`: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @return bool
     */
    public function flush(): bool
    {
        return $this->repository->flush();
    }

    /**
     * @param string $key
     * @param string $metric
     * @param string $binding
     *
     * @return self
     */
    public function bind(string $key, string $metric, string $binding): self
    {
        $this->bindings[$key][$metric] = $binding;

        return $this;
    }

    /**
     * @param string $key
     * @param Metric $metric
     *
     * @return callable
     */
    protected function binding(string $key, Metric $metric): callable
    {
        $bindings = Collection::make($this->bindings[$key]);
        $type = $bindings->keys()->first(function (string $type) use ($metric) {
            return is_subclass_of($metric, $type);
        });

        if ($type === null) {
            throw new RuntimeException("Could not find [{$key}] binding for metric.");
        }

        return new $bindings[$type]($this->repository, "{$this->prefix}:{$metric->key()}");
    }
}
