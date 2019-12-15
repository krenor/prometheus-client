<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\PrometheusException;

class HistogramTest extends TestCase
{
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

        new class extends Histogram {
            protected string $namespace = '';

            protected string $name = '';

            protected array $labels = [
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
     * @group histograms
     */
    public function it_should_throw_an_exception_if_the_minimum_required_bucket_count_is_not_met()
    {
        $this->expectException(PrometheusException::class);
        $this->expectExceptionMessage('Histograms must contain at least one bucket.');

        new class extends Histogram {
            protected string $namespace = '';

            protected string $name = '';

            protected array $buckets = [];
        };
    }

    /**
     * @test
     *
     * @group histograms
     */
    public function it_should_sort_buckets_automatically()
    {
        $histogram = new class extends Histogram {
            protected string $namespace = '';

            protected string $name = '';

            protected array $buckets = [
                5,
                2,
                3,
                1,
                4,
            ];
        };

        $this->assertSame([1, 2, 3, 4, 5], $histogram->buckets()->toArray());
    }
}
