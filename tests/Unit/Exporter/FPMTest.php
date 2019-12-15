<?php

namespace Krenor\Prometheus\Tests\Unit\Exporter;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Exporter\FPM;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;

class FPMTest extends TestCase
{
    /**
     * @var FPM
     */
    private $fpm;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fpm = new FPM($this->getFpmStatusResponse());
    }

    /** @test */
    public function it_should_fetch_the_uptime_metric()
    {
        $family = $this->fpm->uptime();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_uptime_seconds', $sample->name());
        $this->assertSame(302035.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_total_connections_metric()
    {
        $family = $this->fpm->connections();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_connections_total', $sample->name());
        $this->assertSame(44144.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_queued_connections_metric()
    {
        $family = $this->fpm->queued();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_connections_queued_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_max_queued_connections_metric()
    {
        $family = $this->fpm->maxQueued();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_connections_max_queued_count', $sample->name());
        $this->assertSame(1.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_queue_size_metric()
    {
        $family = $this->fpm->queue();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_connections_queue_size', $sample->name());
        $this->assertSame(128.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_idle_processes_metric()
    {
        $family = $this->fpm->idle();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_processes_idle_count', $sample->name());
        $this->assertSame(1.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_active_processes_metric()
    {
        $family = $this->fpm->active();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_processes_active_count', $sample->name());
        $this->assertSame(1.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_total_processes_metric()
    {
        $family = $this->fpm->total();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_processes_total', $sample->name());
        $this->assertSame(2.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_max_active_processes_metric()
    {
        $family = $this->fpm->maxActive();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_processes_max_active_count', $sample->name());
        $this->assertSame(2.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_max_processes_spawned_metric()
    {
        $family = $this->fpm->maxSpawned();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_processes_limit_reached_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_slow_requests_metric()
    {
        $family = $this->fpm->slow();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_connections_slow_count', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www'], $sample->labels()->toArray());
    }

    /** @test */
    public function it_should_fetch_the_processes_metric()
    {
        $metrics = $this->fpm->processes();

        $this->assertSame(8, $metrics->count());

        // Total processed requests of first process
        $family = $metrics->shift();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_requests_total', $sample->name());
        $this->assertSame(22071.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 23], $sample->labels()->toArray());

        // Total request duration of first process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_requests_duration_microseconds', $sample->name());
        $this->assertSame(295.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 23], $sample->labels()->toArray());

        // Total CPU consumed in the last request of first process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_last_cpu_percent', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 23], $sample->labels()->toArray());

        // Total memory consumed in the last request of first process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_last_memory_bytes', $sample->name());
        $this->assertSame(2097152.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 23], $sample->labels()->toArray());

        ##########

        // Total processed requests of second process
        $family = $metrics->shift();

        $this->assertInstanceOf(Counter::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_requests_total', $sample->name());
        $this->assertSame(22073.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 24], $sample->labels()->toArray());

        // Total request duration of second process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_requests_duration_microseconds', $sample->name());
        $this->assertSame(18446744073709550774.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 24], $sample->labels()->toArray());

        // Total CPU consumed in the last request of second process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_last_cpu_percent', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 24], $sample->labels()->toArray());

        // Total memory consumed in the last request of second process
        $family = $metrics->shift();

        $this->assertInstanceOf(Gauge::class, $family->metric());
        $this->assertSame(1, $family->samples()->count());

        /** @var Sample $sample */
        $sample = $family->samples()->first();

        $this->assertSame('php_fpm_process_last_memory_bytes', $sample->name());
        $this->assertSame(0.0, $sample->value());
        $this->assertSame(['pool' => 'www', 'pid' => 24], $sample->labels()->toArray());
    }

    /**
     * @return array
     */
    private function getFpmStatusResponse(): array
    {
        return [
            'pool'                 => 'www',
            //'process-manager'      => 'dynamic',
            //'start-time'           => 1519474655,
            'start-since'          => 302035,
            'accepted-conn'        => 44144,
            'listen-queue'         => 0,
            'max-listen-queue'     => 1,
            'listen-queue-len'     => 128,
            'idle-processes'       => 1,
            'active-processes'     => 1,
            'total-processes'      => 2,
            'max-active-processes' => 2,
            'max-children-reached' => 0,
            'slow-requests'        => 0,

            'processes' => [
                [
                    'pid'                 => 23,
                    //'state'            => 'Idle',
                    //'start-time'       => 1519474655,
                    //'start-since'      => 302035,
                    'requests'            => 22071,
                    'request-duration'    => 295,

                    //'request-method'      => 'GET',
                    //'request-uri'         => '/status?json&full',
                    //'content-length'      => 0,
                    //'user'                => '-',
                    //'script'              => '-',
                    'last-request-cpu'    => 0.00,
                    'last-request-memory' => 2097152,
                ],

                [
                    'pid'                 => 24,
                    //'state'               => 'Running',
                    //'start-time'          => 1519474655,
                    //'start-since'         => 302035,
                    'requests'            => 22073,
                    'request-duration'    => 18446744073709550774,
                    //'request-method'      => 'GET',
                    //'request-uri'         => '/status?json&full',
                    //'content-length'      => 0,
                    //'user'                => '-',
                    //'script'              => '-',
                    'last-request-cpu'    => 0.00,
                    'last-request-memory' => 0,
                ],
            ],
        ];
    }
}
