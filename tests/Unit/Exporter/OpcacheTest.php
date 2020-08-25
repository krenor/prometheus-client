<?php

namespace Krenor\Prometheus\Tests\Unit\Exporter;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Exporter\Opcache;

class OpcacheTest extends TestCase
{
    private Opcache $opcache;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->opcache = new Opcache($this->getOpcacheStatusResponse());
    }

    /** @test */
    public function it_should_fetch_the_enabled_metric()
    {
        $family = $this->opcache->enabled();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_enabled', $sample->name());
        $this->assertSame(1.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_out_of_memory_metric()
    {
        $family = $this->opcache->oom();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_out_of_memory', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_restart_pending_metric()
    {
        $family = $this->opcache->restarts();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_restart_pending', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_restart_in_progress_metric()
    {
        $family = $this->opcache->restarting();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_is_restarting', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_consumed_memory_metric()
    {
        $family = $this->opcache->consumed();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_memory_used_bytes', $sample->name());
        $this->assertSame(12028872.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_free_memory_metric()
    {
        $family = $this->opcache->free();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_memory_free_bytes', $sample->name());
        $this->assertSame(4235696.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_wasted_memory_metric()
    {
        $family = $this->opcache->wasted();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_memory_wasted_bytes', $sample->name());
        $this->assertSame(512648.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_wasted_memory_percent_metric()
    {
        $family = $this->opcache->wastePercentage();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_memory_wasted_percent', $sample->name());
        $this->assertSame(3.0556201934814, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_interned_strings_buffer_size_metric()
    {
        $family = $this->opcache->stringsBuffer();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_strings_buffer_size_bytes', $sample->name());
        $this->assertSame(8388608.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_interned_strings_memory_consumed_metric()
    {
        $family = $this->opcache->stringsMemoryConsumed();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_strings_memory_used_bytes', $sample->name());
        $this->assertSame(458480.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_interned_strings_memory_free_metric()
    {
        $family = $this->opcache->stringsMemoryFree();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_strings_memory_free_bytes', $sample->name());
        $this->assertSame(7930128.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_interned_strings_count_metric()
    {
        $family = $this->opcache->strings();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_strings_count', $sample->name());
        $this->assertSame(5056.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cached_scripts_count_metric()
    {
        $family = $this->opcache->scripts();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_scripts_count', $sample->name());
        $this->assertSame(59.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cached_keys_count_metric()
    {
        $family = $this->opcache->keys();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_keys_count', $sample->name());
        $this->assertSame(78.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_maximum_cached_keys_count_metric()
    {
        $family = $this->opcache->maxCachedKeys();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_max_keys_count', $sample->name());
        $this->assertSame(223.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cache_hits_count_metric()
    {
        $family = $this->opcache->hits();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_hits_count', $sample->name());
        $this->assertSame(66817.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_start_metric()
    {
        $family = $this->opcache->started();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_started', $sample->name());
        $this->assertSame(1410858101.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_last_restart_metric()
    {
        $family = $this->opcache->restart();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_last_restart', $sample->name());
        $this->assertSame(1410915824.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_out_of_memory_restarts_count_metric()
    {
        $family = $this->opcache->oomRestarts();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_restarts_oom_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_hash_restarts_count_metric()
    {
        $family = $this->opcache->hashRestarts();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_restarts_hash_count', $sample->name());
        $this->assertSame(1.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_manual_restarts_count_metric()
    {
        $family = $this->opcache->manualRestarts();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_restarts_manual_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cache_misses_count_metric()
    {
        $family = $this->opcache->misses();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_misses_count', $sample->name());
        $this->assertSame(126.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cache_blacklist_misses_count_metric()
    {
        $family = $this->opcache->blacklistMisses();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_blacklist_misses_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cache_blacklist_misses_percent_metric()
    {
        $family = $this->opcache->blacklistMissRatio();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_blacklist_misses_percent', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_cache_hit_percent_metric()
    {
        $family = $this->opcache->hitRatio();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_opcache_cache_hit_percent', $sample->name());
        $this->assertSame(99.81178017119, $sample->value());
        $this->assertEmpty($sample->labels()->toArray());
    }

    /**
     * @return array
     */
    private function getOpcacheStatusResponse(): array
    {
        return [
            'opcache_enabled'     => true,
            'cache_full'          => false,
            'restart_pending'     => false,
            'restart_in_progress' => false,

            'memory_usage' => [
                'used_memory'               => 12028872,
                'free_memory'               => 4235696,
                'wasted_memory'             => 512648,
                'current_wasted_percentage' => 3.0556201934814,
            ],

            'interned_strings_usage' => [
                'buffer_size'       => 8388608,
                'used_memory'       => 458480,
                'free_memory'       => 7930128,
                'number_of_strings' => 5056,
            ],

            'opcache_statistics' => [
                'num_cached_scripts'   => 59,
                'num_cached_keys'      => 78,
                'max_cached_keys'      => 223,
                'hits'                 => 66817,
                'start_time'           => 1410858101,
                'last_restart_time'    => 1410915824,
                'oom_restarts'         => 0,
                'hash_restarts'        => 1,
                'manual_restarts'      => 0,
                'misses'               => 126,
                'blacklist_misses'     => 0,
                'blacklist_miss_ratio' => 0,
                'opcache_hit_rate'     => 99.81178017119,
            ],

            /*
            'scripts' => [
                '/var/www/opcache.php' => [
                    'full_path'           => '/var/www/opcache.php',
                    'hits'                => 0,
                    'memory_consumption'  => 1064,
                    'last_used'           => 'Tue Sep 16 09:01:41 2014',
                    'last_used_timestamp' => 1410858101,
                    'timestamp'           => 1410858099,
                ],
            ],
            */
        ];
    }
}
