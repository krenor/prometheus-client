<?php

namespace Krenor\Prometheus\Storage;

use Exception;
use Predis\Client as Redis;
use Krenor\Prometheus\Sample;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Contracts\Storage;
use Tightenco\Collect\Support\Collection;
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
    public function collect(Metric $metric): Collection
    {
        $key = $this->key($metric);

        try {
            $items = new Collection($this->redis->hgetall($key));

            // TODO: Might want to use Observable instead. Check back when working with Summaries.
            if ($metric instanceof Histogram) {
                $items = $items->merge($this->redis->hgetall("{$key}:SUM"));
            }

            return $items->map(function (string $value, string $key) {
                return new Sample($value, new Collection(json_decode($key, true)));
            })->values();
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
            $key = $this->key($metric);

            $this->redis->hincrbyfloat($key, $this->field($metric, $labels), $value);
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
            $key = $this->key($metric);

            $this->redis->hincrbyfloat($key, $this->field($metric, $labels, $value), 1);
            $this->redis->hincrbyfloat("{$key}:SUM", $this->field($metric, $labels), $value);
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
            $key = $this->key($gauge);

            $this->redis->hset($key, $this->field($gauge, $labels), $value);
        } catch (Exception $e) {
            $class = get_class($gauge);

            throw new StorageException("Failed to set the value of [$class] to `$value`.", 0, $e);
        }
    }
}
