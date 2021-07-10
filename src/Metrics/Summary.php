<?php

namespace Krenor\Prometheus\Metrics;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\PrometheusException;
use Krenor\Prometheus\Metrics\Concerns\TracksExecutionTime;

abstract class Summary extends Metric implements Observable
{
    use TracksExecutionTime;

    protected array $quantiles = [
        .01,
        .05,
        .5,
        .9,
        .99,
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        foreach ($this->labels as $label) {
            if (preg_match('/^quantile$/', $label)) {
                throw new LabelException('The label `quantile` is used internally to designate summary quantiles.');
            }
        }

        foreach ($this->quantiles as $quantile) {
            if ($quantile < 0 || $quantile > 1) {
                throw new PrometheusException('Quantiles have to be in the range between 0 and 1.');
            }
        }

        sort($this->quantiles);
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return 'summary';
    }

    /**
     * {@inheritdoc}
     */
    public function observe(float $value, array $labels = []): static
    {
        static::$storage->observe($this, $value, $labels);

        return $this;
    }

    /**
     * @return Collection
     */
    public function quantiles(): Collection
    {
        return new Collection($this->quantiles);
    }

    /**
     * {@inheritdoc}
     */
    protected function track(float $value, array $labels = []): void
    {
        $this->observe($value, $labels);
    }
}
