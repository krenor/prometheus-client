<?php

namespace Krenor\Prometheus\Storage;

use Exception;
use Predis\Client as Redis;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Contracts\Storage;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;
use Krenor\Prometheus\Storage\Concerns\InteractsWithStoredMetrics;

class RedisStorage implements Storage
{
    use InteractsWithStoredMetrics, StoresMetrics;

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
    public function collect(Metric $metric): Collection
    {
        $key = $this->prefixed($this->key($metric));

        try {
            $items = new Collection($this->redis->hgetall($key));

            // TODO: Sort by label values?
            switch (true) {
                case $metric instanceof Histogram:
                    return $this->samples($metric, $items->merge($this->redis->hgetall("{$key}:SUM")));
                default:
                    return $this->samples($metric, $items);
            }
        } catch (Exception $e) {
            throw new StorageException("Failed to collect `{$key}` samples.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function increment(Incrementable $metric, float $value, array $labels): void
    {
        try {
            $this->redis->hincrbyfloat(
                $this->prefixed($this->key($metric)),
                $this->labeled($metric, $labels)->toJson(),
                $value
            );
        } catch (Exception $e) {
            $class = get_class($metric);
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
        try {
            $key = $this->prefixed($this->key($metric));
            $labeled = $this->labeled($metric, $labels);
            $field = $labeled->toJson();

            if ($metric instanceof Histogram) {
                $this->redis->hincrbyfloat($key, $labeled->merge($this->bucket($metric, $value))->toJson(), 1);
                $this->redis->hincrbyfloat("{$key}:SUM", $field, $value);
            }

            if ($metric instanceof Summary) {
                $identifier = "$key:" . crc32($field) . ':VALUES';

                $this->redis->hsetnx($key, $field, $identifier);
                $this->redis->rpush($identifier, $value);
            }
        } catch (Exception $e) {
            $class = get_class($metric);

            throw new StorageException("Failed to observe [$class] with a value of `$value`.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        try {
            $this->redis->hset(
                $this->prefixed($this->key($gauge)),
                $this->labeled($gauge, $labels)->toJson(),
                $value
            );
        } catch (Exception $e) {
            $class = get_class($gauge);

            throw new StorageException("Failed to set the value of [$class] to `$value`.", 0, $e);
        }
    }
}
