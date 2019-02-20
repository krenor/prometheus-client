<?php

namespace Krenor\Prometheus\Exporter;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Contracts\Exporter;
use Krenor\Prometheus\MetricFamilySamples;

class Opcache extends Exporter
{
    /**
     * @return MetricFamilySamples
     */
    public function enabled(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'enabled';
                protected $description = 'Indicator if opcache is enabled.';
            },
            $this->data['opcache_enabled']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function oom(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'out_of_memory';
                protected $description = 'Indicator if cache is full.';
            },
            $this->data['cache_full']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function restarts(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'restart_pending';
                protected $description = 'Indicator if a restart is pending';
            },
            $this->data['restart_pending']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function restarting(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'is_restarting';
                protected $description = 'Indicator if a restart is in progress.';
            },
            $this->data['restart_in_progress']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function consumed(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'memory_used_bytes';
                protected $description = 'The amount of memory consumed.';
            },
            $this->data['memory_usage']['used_memory']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function free(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'memory_free_bytes';
                protected $description = 'The amount of memory available for consumption.';
            },
            $this->data['memory_usage']['free_memory']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function wasted(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'memory_wasted_bytes';
                protected $description = 'The amount of memory wasted.';
            },
            $this->data['memory_usage']['wasted_memory']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function wastePercentage(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'memory_wasted_percent';
                protected $description = 'The percentage of currently wasted memory.';
            },
            $this->data['memory_usage']['current_wasted_percentage']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function stringsBuffer(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'strings_buffer_size_bytes';
                protected $description = 'The buffer size of interned strings.';
            },
            $this->data['interned_strings_usage']['buffer_size']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function stringsMemoryConsumed(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'strings_memory_used_bytes';
                protected $description = 'The amount of memory used by interned strings.';
            },
            $this->data['interned_strings_usage']['used_memory']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function stringsMemoryFree(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'strings_memory_free_bytes';
                protected $description = 'The amount of memory available for interned strings.';
            },
            $this->data['interned_strings_usage']['free_memory']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function strings(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'strings_count';
                protected $description = 'The amount of used interned strings';
            },
            $this->data['interned_strings_usage']['number_of_strings']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function scripts(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_scripts_count';
                protected $description = 'The amount of cached scripts.';
            },
            $this->data['opcache_statistics']['num_cached_scripts']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function keys(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_keys_count';
                protected $description = 'The amount of hash table keys.';
            },
            $this->data['opcache_statistics']['num_cached_keys']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function maxCachedKeys(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_max_keys_count';
                protected $description = 'The maximum amount of hash table keys.';
            },
            $this->data['opcache_statistics']['max_cached_keys']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function hits(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_hits_count';
                protected $description = 'The amount of cache hits.';
            },
            $this->data['opcache_statistics']['hits']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function started(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'started';
                protected $description = 'The timestamp opcache has been started.';
            },
            $this->data['opcache_statistics']['start_time']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function restart(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'last_restart';
                protected $description = 'The last timestamp opcache has been restarted.';
            },
            $this->data['opcache_statistics']['last_restart_time']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function oomRestarts(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'restarts_oom_count';
                protected $description = 'The amount of out of memory restarts.';
            },
            $this->data['opcache_statistics']['oom_restarts']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function hashRestarts(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'restarts_hash_count';
                protected $description = 'The amount of hash table overflow restarts.';
            },
            $this->data['opcache_statistics']['hash_restarts']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function manualRestarts(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'restarts_manual_count';
                protected $description = 'The amount of manual restarts.';
            },
            $this->data['opcache_statistics']['manual_restarts']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function misses(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_misses_count';
                protected $description = 'The amount of cache misses.';
            },
            $this->data['opcache_statistics']['misses']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function blacklistMisses(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_blacklist_misses_count';
                protected $description = 'The amount of blacklist cache misses.';
            },
            $this->data['opcache_statistics']['blacklist_misses']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function blacklistMissRatio(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_blacklist_misses_percent';
                protected $description = 'The percentage of blacklist cache misses. ';
            },
            $this->data['opcache_statistics']['blacklist_miss_ratio']
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function hitRatio(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected $namespace = 'php_opcache';
                protected $name = 'cache_hit_percent';
                protected $description = 'The percentage of cache hits.';
            },
            $this->data['opcache_statistics']['opcache_hit_rate']
        );
    }
}
