<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

interface Storage
{
    // TODO: Define
    public function collect();

    /**
     * @param Incrementable $metric
     * @param float $value
     * @param array $labels
     *
     * @throws StorageException
     *
     * @return void
     */
    public function increment(Incrementable $metric, float $value, array $labels): void;

    /**
     * @param Decrementable $metric
     * @param float $value
     * @param array $labels
     *
     * @throws StorageException
     *
     * @return void
     */
    public function decrement(Decrementable $metric, float $value, array $labels): void;

    /**
     * @param Observable $metric
     * @param float $value
     * @param array $labels
     *
     * @throws StorageException
     *
     * @return void
     */
    public function observe(Observable $metric, float $value, array $labels): void;

    /**
     * @param Gauge $gauge
     * @param float $value
     * @param array $labels
     *
     * @throws StorageException
     *
     * @return void
     */
    public function set(Gauge $gauge, float $value, array $labels): void;
}
