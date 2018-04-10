<?php

namespace Krenor\Prometheus\Storage;

use Exception;
use Predis\Client as Redis;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Storage;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

class RedisStorage implements Storage
{
    use StoresMetrics;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * RedisStorage constructor.
     *
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        // TODO: Implement collect() method.
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels): void
    {
        $class = get_class($metric);

        try {
            $key = $this->key($metric);

            $this->meta($key, $class);
            $this->redis->hincrbyfloat($key, $this->field($metric, $labels), $value);
        } catch (Exception $e) {
            $operation = __METHOD__;

            throw new StorageException("Failed to {$operation} [$class] by `{$value}`.", 0, $e);
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
        $class = get_class($metric);

        try {
            $key = $this->key($metric);

            $this->meta($key, $class);
            $this->redis->hincrbyfloat($key, $this->field($metric, $labels, $value), 1);
            $this->redis->hincrbyfloat("{$key}:SUM", $this->field($metric, $labels), $value);
        } catch (Exception $e) {
            throw new StorageException("Failed to observe [$class] with a value of `$value`.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        $class = get_class($gauge);

        try {
            $key = $this->key($gauge);

            $this->meta($key, $class);
            $this->redis->hset($key, $this->field($gauge, $labels), $value);
        } catch (Exception $e) {
            throw new StorageException("Failed to set the value of [$class] to `$value`.", 0, $e);
        }
    }

    /**
     * @param string $key
     * @param string $namespace
     */
    private function meta(string $key, string $namespace): void
    {
        $this->redis->hsetnx($key, '__namespace', $namespace);
    }
}
