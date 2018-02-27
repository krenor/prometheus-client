<?php

namespace Krenor\Prometheus;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Storage;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Metric;

class CollectorRegistry
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var Collection
     */
    protected $counters;

    /**
     * @var Collection
     */
    protected $gauges;

    /**
     * @var Collection
     */
    protected $histograms;

    /**
     * @var Collection
     */
    protected $summaries;

    /**
     * CollectorRegistry constructor.
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->counters = new Collection;
        $this->gauges = new Collection;
        $this->histograms = new Collection;
        $this->summaries = new Collection;
    }

    public function collect()
    {
        // TODO: Implement collect() method.
    }

    /**
     * @param Metric $metric
     *
     * @return Metric
     */
    public function register(Metric $metric): Metric
    {
        $collector = $this->collector($metric);
        $key = $metric->key();

        if (!$collector->contains($key)) {
            $collector->put($this->key($metric), $metric);
        }

        return $collector->get($key);
    }

    /**
     * @param Metric $metric
     *
     * @return CollectorRegistry
     */
    public function unregister(Metric $metric): self
    {
        $this->collector($metric)->forget(
            $metric->key()
        );

        return $this;
    }

    /**
     * @return Storage
     */
    public function storage()
    {
        return $this->storage;
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
     * @param string $key
     *
     * @return Counter|null
     */
    public function counter(string $key): ?Counter
    {
        return $this->counters->get($key);
    }

    /**
     * @param string $key
     *
     * @return Gauge|null
     */
    public function gauge(string $key): ?Gauge
    {
        return $this->gauges->get($key);
    }

    /**
     * @param string $key
     *
     * @return Histogram|null
     */
    public function histogram(string $key): ?Histogram
    {
        return $this->histograms->get($key);
    }

    /**
     * @param string $key
     *
     * @return Summary|null
     */
    public function summary(string $key): ?Summary
    {
        return $this->summaries->get($key);
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
        }
    }
}
