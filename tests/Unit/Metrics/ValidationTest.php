<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Metrics\Histogram;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\PrometheusException;

class ValidationTest extends BaseTestCase
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

        new class extends Metric
        {
            protected $labels = [
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

        new class extends Metric
        {
            protected $namespace = 'valid';

            protected $name = 'învälíd';

            protected $labels = [];

            public function type(): string {}
        };
    }

    /**
     * @test
     *
     * @group exceptions
     * @group histograms
     */
    public function it_should_throw_an_exception_if_a_histogram_uses_the_le_label()
    {
        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('The label `le` is used internally to designate buckets.');

        new class extends Histogram
        {
            protected $namespace = '';

            protected $name = '';

            protected $labels = [
                'lel',
                'le',
                'lul',
            ];
        };
    }

    /**
     * @test
     *
     * @group exceptions
     * @group summaries
     */
    public function it_should_throw_an_exception_if_a_summary_uses_the_quantile_label()
    {
        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('The label `quantile` is used internally to designate summary quantiles.');

        new class extends Summary
        {
            protected $namespace = '';

            protected $name = '';

            protected $labels = [
                'quentin',
                'quantum',
                'quantile',
            ];
        };
    }
}
