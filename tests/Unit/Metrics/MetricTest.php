<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use Krenor\Prometheus\Metrics\Metric;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\PrometheusException;

class MetricTest extends BaseTestCase
{
    /**
     * @test
     *
     * @group exceptions
     */
    public function it_should_throw_an_exception_if_the_label_names_are_invalid()
    {
        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('The label `__in-val:id` contains invalid characters.');

        new class extends Metric {
            protected array $labels = [
                'valid',
                '__in-val:id',
            ];

            public function type(): string {}
        };
    }

    /**
     * @test
     *
     * @group exceptions
     */
    public function it_should_throw_an_exception_if_metric_key_is_invalid()
    {
        $this->expectException(PrometheusException::class);
        $this->expectExceptionMessage('The metric name `valid_învälíd` contains invalid characters.');

        new class extends Metric {
            protected ?string $namespace = 'valid';

            protected string $name = 'învälíd';

            protected array $labels = [];

            public function type(): string {}
        };
    }
}
