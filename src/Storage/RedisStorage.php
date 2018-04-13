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
        $name = "{$metric->namespace()}:{$metric->name()}";

        try {
            $items = new Collection($this->redis->hgetall($key));

            $labeled = $items->mapToGroups(function (string $value, string $key) {
                $raw = json_decode($key, true);

                return [
                    json_encode($raw['labels']) => compact('value') + $raw,
                ];
            });

            // TODO: Might want to use Observable instead. Check back when working with Summaries.
            if ($metric instanceof Histogram) {
                $labeled = $this->transformHistograms($labeled, new Collection($metric->buckets()));
            }

            return $labeled->flatMap(function (Collection $group) use ($metric, $name) {
                return $group->map(function (array $item) use ($metric, $name) {
                    $value = $item['value'];
                    $labels = new Collection($item['labels']);

                    if ($metric instanceof Histogram) {
                        // TODO: _count and _sum, too!
                        return new Sample("{$name}_bucket", $value, $labels->put('le', $item['bucket']));
                    }

                    return new Sample($name, $value, $labels);
                });
            });
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

    /**
     * @param Collection $labeled
     * @param Collection $buckets
     *
     * @return Collection
     */
    // TODO: This might be better placed in the trait.
    // TODO: This might also be better if handled by a fractal transformer. Future me, take care!
    private function transformHistograms(Collection $labeled, Collection $buckets): Collection
    {
        // Serving as "+Inf" bucket.
        $buckets->push(PHP_INT_MAX);

        return $labeled->map(function (Collection $items) use ($buckets) {
            $labels = $items->first()['labels'];

            // The stored bucket containing "+Inf" has to be excluded because it's handled separately.
            $sets = $items->reject(function ($data) {
                return $data['bucket'] === '+Inf';
            });

            $missing = $buckets
                ->diff($items->pluck('bucket'))
                ->map(function (float $bucket) use ($items, $sets) {
                    // Use the value of the previous bucket or default to 0.
                    $value = $sets->where('bucket', '<', $bucket)->last()['value'] ?? 0;

                    // If the "+Inf" bucket is stored use its value instead of using
                    // the previous bucket's value for the "pseudo" +Inf bucket.
                    if ($bucket === (float) PHP_INT_MAX) {
                        $bucket = '+Inf';

                        $key = $items->search(function (array $item) {
                            return $item['bucket'] === '+Inf';
                        });

                        if ($key !== false) {
                            $value = $items->get($key)['value'];
                        }
                    }

                    return compact('bucket', 'labels', 'value');
                });

            return $sets
                ->merge($missing)
                ->map(function (array $item) use ($labels) {
                    $item['labels'] = $labels;

                    return $item;
                })->sort(function (array $left, array $right) {
                    // Due to http://php.net/manual/en/language.types.string.php#language.types.string.conversion the
                    // bucket containing "+Inf" will be cast to 0. Sorting regularly would end up with it incorrectly
                    // sitting at the very first spot instead of where it belongs - at the end.
                    if ($left['bucket'] === '+Inf') {
                        return 1;
                    }

                    if ($right['bucket'] === '+Inf') {
                        return -1;
                    }

                    return $left['bucket'] <=> $right['bucket'];
                })->values();
        });
    }
}
