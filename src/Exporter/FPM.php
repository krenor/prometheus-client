<?php

namespace Krenor\Prometheus\Exporter;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Contracts\Exporter;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;

class FPM extends Exporter
{
    /**
     * @return MetricFamilySamples
     */
    public function uptime(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'uptime_seconds';
                protected string $description = 'The number of seconds since FPM has started.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['start-since'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function connections(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'connections_total';
                protected string $description = 'The number of requests accepted.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['accepted-conn'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function queued(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected string $namespace = 'php_fpm';
                protected string $name = 'connections_queued_count';
                protected string $description = 'The number of request in the queue of pending connections.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['listen-queue'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function maxQueued(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'connections_max_queued_count';
                protected string $description = 'The maximum number of requests in the queue of pending connections.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['max-listen-queue'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function queue(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected string $namespace = 'php_fpm';
                protected string $name = 'connections_queue_size';
                protected string $description = 'The size of the socket queue for pending connections.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['listen-queue-len'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function idle(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected string $namespace = 'php_fpm';
                protected string $name = 'processes_idle_count';
                protected string $description = 'The number of idle processes.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['idle-processes'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function active(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected string $namespace = 'php_fpm';
                protected string $name = 'processes_active_count';
                protected string $description = 'The number of active processes.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['active-processes'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function total(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Gauge {
                protected string $namespace = 'php_fpm';
                protected string $name = 'processes_total';
                protected string $description = 'The number of idle and active processes.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['total-processes'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function maxActive(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'processes_max_active_count';
                protected string $description = 'The maximum number of active processes since FPM has started.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['max-active-processes'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function maxSpawned(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'processes_limit_reached_count';
                protected string $description = 'The number of times the process limit has been reached when trying to start more children.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['max-children-reached'],
            [$this->data['pool']]
        );
    }

    /**
     * @return MetricFamilySamples
     */
    public function slow(): MetricFamilySamples
    {
        return $this->sampled(
            new class extends Counter {
                protected string $namespace = 'php_fpm';
                protected string $name = 'connections_slow_count';
                protected string $description = 'The number of requests exceeding the configured \'request_slowlog_timeout\' value.';
                protected array $labels = [
                    'pool',
                ];
            },
            $this->data['slow-requests'],
            [$this->data['pool']]
        );
    }

    /**
     * @return Collection|Metric[]
     */
    public function processes(): Collection
    {
        return (new Collection($this->data['processes']))
            ->map(function (array $process) {
                $labels = [
                    $this->data['pool'],
                    $process['pid'],
                ];

                return new Collection([
                    $this->sampled(
                        new class extends Counter {
                            protected string $namespace = 'php_fpm';
                            protected string $name = 'process_requests_total';
                            protected string $description = 'The number of requests the process has served.';
                            protected array $labels = [
                                'pool',
                                'pid',
                            ];
                        },
                        $process['requests'],
                        $labels
                    ),

                    $this->sampled(
                        new class extends Gauge {
                            protected string $namespace = 'php_fpm';
                            protected string $name = 'process_requests_duration_microseconds';
                            protected string $description = 'The duration in microseconds of the requests.';
                            protected array $labels = [
                                'pool',
                                'pid',
                            ];
                        },
                        $process['request-duration'],
                        $labels
                    ),

                    $this->sampled(
                        new class extends Gauge {
                            protected string $namespace = 'php_fpm';
                            protected string $name = 'process_last_cpu_percent';
                            protected string $description = 'The percentage of cpu the last request consumed.';
                            protected array $labels = [
                                'pool',
                                'pid',
                            ];
                        },
                        $process['last-request-cpu'],
                        $labels
                    ),

                    $this->sampled(
                        new class extends Gauge {
                            protected string $namespace = 'php_fpm';
                            protected string $name = 'process_last_memory_bytes';
                            protected string $description = 'The amount of memory the last request consumed.';
                            protected array $labels = [
                                'pool',
                                'pid',
                            ];
                        },
                        $process['last-request-memory'],
                        $labels
                    ),
                ]);
            })->collapse();
    }
}
