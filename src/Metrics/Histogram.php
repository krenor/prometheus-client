<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\PrometheusException;

abstract class Histogram extends Metric implements Observable
{
    /**
     * @var float[]
     */
    protected $buckets = [
        .005,
        .01,
        .025,
        .05,
        .1,
        .25,
        .5,
        1,
        2.5,
        5,
        10,
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        foreach ($this->labels as $label) {
            if (preg_match('/^le$/', $label)) {
                throw new LabelException('The label `le` is used internally to designate buckets.');
            }
        }

        if (count($this->buckets) < 1) {
            throw new PrometheusException('Histograms must contain at least one bucket.');
        }

        sort($this->buckets);
    }

    /**
     * {@inheritdoc}
     */
    final public function type(): string
    {
        return 'histogram';
    }

    /**
     * {@inheritdoc}
     */
    public function observe(float $value, array $labels): self
    {
        static::$storage->observe($this, $value, $labels);

        return $this;
    }

    /**
     * @return Collection
     */
    public function buckets(): Collection
    {
        return new Collection($this->buckets);
    }
}
