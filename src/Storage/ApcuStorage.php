<?php

namespace Krenor\Prometheus\Storage;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Storage;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;

class ApcuStorage implements Storage
{
    use StoresMetrics;

    /**
     * {@inheritdoc}
     */
    public function collect(Metric $metric): Collection
    {
        // TODO: Implement collect() method.
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels): void
    {
        $key = $this->key($metric);
        $class = get_class($metric);
        $identifier = base64_encode($this->field($metric, $labels));

        if (!apcu_store($key, $this->meta($this->update($key, $identifier, $value), $class))) {
            $operation = __METHOD__;

            throw new StorageException("Failed to {$operation} [$class] by `{$value}`.");
        }
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
        $success = [];
        $key = $this->key($metric);
        $class = get_class($metric);
        $identifier = base64_encode($this->field($metric, $labels, $value));

        $sumKey = "{$key}:SUM";
        $sumIdentifier = base64_encode($this->field($metric, $labels));

        $success[] = apcu_store($key, $this->meta($this->update($key, $identifier, 1), $class));
        $success[] = apcu_store($sumKey, $this->update($sumKey, $sumIdentifier, $value));

        if (in_array(false, $success)) {
            throw new StorageException("Failed to observe [$class] with a value of `$value`.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        $key = $this->key($gauge);
        $class = get_class($gauge);
        $identifier = base64_encode($this->field($gauge, $labels));

        if (!apcu_store($key, $this->meta($this->update($key, $identifier, $value, true), $class))) {
            throw new StorageException("Failed to set the value of [$class] to `$value`.");
        }
    }

    /**
     * @param string $key
     * @param string $identifier
     * @param float $value
     * @param bool $overwrite
     *
     * @return array
     */
    private function update(string $key, string $identifier, float $value, bool $overwrite = false): array
    {
        $item = apcu_fetch($key) ?: [];

        if (!array_key_exists($identifier, $item)) {
            $item[$identifier] = 0;
        }

        if ($overwrite) {
            $item[$identifier] = $value;
        } else {
            $item[$identifier] += $value;
        }

        return $item;
    }

    /**
     * @param array $item
     * @param string $namespace
     *
     * @return array
     */
    private function meta(array $item, string $namespace): array
    {
        if (!array_key_exists('__namespace', $item)) {
            return $item + ['__namespace' => $namespace];
        }

        return $item;
    }
}
