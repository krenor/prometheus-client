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
     * @var string
     */
    protected $prefix;

    /**
     * RedisStorage constructor.
     *
     * @param Redis $redis
     * @param string $prefix
     */
    public function __construct(Redis $redis, $prefix = '')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
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
        try {
            $this->redis->hincrbyfloat($this->key($metric), $this->field($metric, $labels), $value);
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

            // TODO: Wording?
            throw new StorageException("Failed to observe [$class] with a value of `$value`.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(Gauge $gauge, float $value, array $labels): void
    {
        /**
            try {
                $this->redis->hset($this->key($gauge), $this->field($gauge, $labels), $value);
            } catch (Exception $e) {
                $class = get_class($gauge);

                throw new StorageException("Failed to set the value of [$class] to `$value`.", 0, $e);
            }
        */
    }
}
