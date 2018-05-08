<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Exceptions\LabelException;

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
     * @group histograms
     */
    public function it_should_sort_buckets_automatically()
    {
        $histogram = new class extends Histogram
        {
            protected $namespace = '';

            protected $name = '';

            protected $buckets = [
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
