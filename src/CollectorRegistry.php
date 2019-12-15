<?php

namespace Krenor\Prometheus;

use InvalidArgumentException;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;

class CollectorRegistry
{
    protected Collection $counters;

    protected Collection $gauges;

    protected Collection $histograms;

    protected Collection $summaries;

    /**
     * CollectorRegistry constructor.
     */
    public function __construct()
    {
        $this->counters = new Collection;
        $this->gauges = new Collection;
        $this->histograms = new Collection;
        $this->summaries = new Collection;
    }

    /**
     * @return Collection
     */
    public function collect(): Collection
    {
        return $this
            ->collectible()
            ->collapse()
            ->map(function (Metric $metric) {
                return new MetricFamilySamples($metric, $metric::storage()->collect($metric));
            });
    }

    /**
     * @param Metric $metric
     *
     * @return Metric
     */
    public function register(Metric $metric): Metric
    {
        $collector = $this->collector($metric);
        $namespace = get_class($metric);

        if (!$collector->contains($namespace)) {
            $collector->put($namespace, $metric);
        }

        return $collector->get($namespace);
    }

    /**
     * @param Metric $metric
     *
     * @return self
     */
    public function unregister(Metric $metric): self
    {
        $this->collector($metric)
             ->forget(get_class($metric));

        return $this;
    }

    /**
     * @return Collection
     */
    public function counters(): Collection
    {
        return $this->counters;
    }

    /**
     * @return Collection
     */
    public function gauges(): Collection
    {
        return $this->gauges;
    }

    /**
     * @return Collection
     */
    public function histograms(): Collection
    {
        return $this->histograms;
    }

    /**
     * @return Collection
     */
    public function summaries(): Collection
    {
        return $this->summaries;
    }

    /**
     * @param string $namespace
     *
     * @return Counter|null
     */
    public function counter(string $namespace): ?Counter
    {
        return $this->counters->get($namespace);
    }

    /**
     * @param string $namespace
     *
     * @return Gauge|null
     */
    public function gauge(string $namespace): ?Gauge
    {
        return $this->gauges->get($namespace);
    }

    /**
     * @param string $namespace
     *
     * @return Histogram|null
     */
    public function histogram(string $namespace): ?Histogram
    {
        return $this->histograms->get($namespace);
    }

    /**
     * @param string $namespace
     *
     * @return Summary|null
     */
    public function summary(string $namespace): ?Summary
    {
        return $this->summaries->get($namespace);
    }

    /**
     * @param Metric $metric
     *
     * @return Collection
     */
    protected function collector(Metric $metric): Collection
    {
        switch ($metric) {
            case $metric instanceof Counter:
                return $this->counters;
            case $metric instanceof Gauge:
                return $this->gauges;
            case $metric instanceof Histogram:
                return $this->histograms;
            case $metric instanceof Summary:
                return $this->summaries;
            default:
                throw new InvalidArgumentException("Could not determine collector of unknown metric type.");
        }
    }

    /**
     * @return Collection|Collection[]
     */
    protected function collectible(): Collection
    {
        return new Collection([
            $this->counters,
            $this->gauges,
            $this->histograms,
            $this->summaries,
        ]);
    }
}
