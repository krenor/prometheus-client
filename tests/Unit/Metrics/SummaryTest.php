<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\PrometheusException;

class SummaryTest extends TestCase
{
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

    /**
     * @test
     *
     * @group exceptions
     * @group summaries
     */
    public function it_should_throw_an_exception_if_a_quantile_is_outside_the_valid_range()
    {
        $this->expectException(PrometheusException::class);
        $this->expectExceptionMessage('Quantiles have to be in the range between 0 and 1.');

        new class extends Summary
        {
            protected $namespace = '';

            protected $name = '';

            protected $quantiles = [
                .5,
                1.5,
                3,
            ];
        };
    }

    /**
     * @test
     *
     * @group summaries
     */
    public function it_should_sort_quantiles_automatically()
    {
        $summary = new class extends Summary
        {
            protected $namespace = '';

            protected $name = '';

            protected $quantiles = [
                .4,
                .3,
                .2,
                .1,
            ];
        };

        $this->assertSame([.1, .2, .3, .4], $summary->quantiles()->toArray());
    }
}
