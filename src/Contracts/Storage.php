<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Types\Settable;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

interface Storage
{
    /**
     * @param Metric $metric
     *
     * @throws StorageException
     *
     * @return Collection
     */
    public function collect(Metric $metric): Collection;

    /**
     * @param Incrementable $metric
     * @param float $value
     * @param array $labels
     *
     * @throws LabelException
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
     * @throws LabelException
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
     * @throws LabelException
     * @throws StorageException
     *
     * @return void
     */
    public function observe(Observable $metric, float $value, array $labels): void;

    /**
     * @param Settable $metric
     * @param float $value
     * @param array $labels
     *
     * @throws LabelException
     * @throws StorageException
     *
     * @return void
     */
    public function set(Settable $metric, float $value, array $labels): void;
}
