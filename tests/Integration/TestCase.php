<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\CollectorRegistry;
use Krenor\Prometheus\Metrics\Histogram;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Renderer\TextRenderer;
use Krenor\Prometheus\Storage\StorageManager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelSummaryStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsGaugeStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsCounterStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsSummaryStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsHistogramStub;

abstract class TestCase extends BaseTestCase
{
    protected static Repository $repository;

    private CollectorRegistry $registry;

    private array $labels = [
        'multi'  => [
            'one'  => ['one', 'two', 'three'],
            'foo'  => ['foo', 'bar', 'baz'],
            'beep' => ['beep', 'boop', 'robot'],
        ],
        'single' => [
            'hello' => ['hello world'],
            'lorem' => ['lorem ipsum'],
            'fizz'  => ['fizz buzz'],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new CollectorRegistry;

        Metric::storeUsing(new StorageManager(static::$repository));
    }

    /** @test */
    public function it_should_increment_counters_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelCounterStub);
        $multi = $this->registry->register(new MultipleLabelsCounterStub);

        $inc = function (Counter $counter, array $values, array $labels) {
            foreach ($values as $value) {
                $counter->incrementBy($value, $labels);
            }
        };

        $inc($single, [2, 3], $this->labels['single']['hello']);
        $inc($single, [7], $this->labels['single']['lorem']);
        $inc($single, [1, 1], $this->labels['single']['fizz']);

        $inc($multi, [2, 3, 2], $this->labels['multi']['one']);
        $inc($multi, [2, 2, 2, 2], $this->labels['multi']['foo']);
        $inc($multi, [1], $this->labels['multi']['beep']);

        $this->compare('counters');
    }

    /** @test */
    public function it_should_increment_gauges_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelGaugeStub);
        $multi = $this->registry->register(new MultipleLabelsGaugeStub);

        $inc = function (Gauge $gauge, array $values, array $labels) {
            foreach ($values as $value) {
                $gauge->incrementBy($value, $labels);
            }
        };

        $inc($single, [5], $this->labels['single']['hello']);
        $inc($single, [3, 3, 1], $this->labels['single']['lorem']);
        $inc($single, [2], $this->labels['single']['fizz']);

        $inc($multi, [6, 1], $this->labels['multi']['one']);
        $inc($multi, [4, 4], $this->labels['multi']['foo']);
        $inc($multi, [1], $this->labels['multi']['beep']);

        $this->compare('gauges');
    }

    /** @test */
    public function it_should_decrement_gauges_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelGaugeStub);
        $multi = $this->registry->register(new MultipleLabelsGaugeStub);

        $inc = function (Gauge $gauge, array $values, array $labels) {
            foreach ($values as $value) {
                $gauge->incrementBy($value, $labels);
            }
        };

        $dec = function (Gauge $gauge, array $values, array $labels) {
            foreach ($values as $value) {
                $gauge->decrementBy($value, $labels);
            }
        };

        $inc($single, [1, 2, 3, 4, 5], $this->labels['single']['hello']);
        $dec($single, [5, 5], $this->labels['single']['hello']);
        $inc($single, [7, 1], $this->labels['single']['lorem']);
        $dec($single, [1], $this->labels['single']['lorem']);
        $inc($single, [2, 3, 4], $this->labels['single']['fizz']);
        $dec($single, [2, 3, 2], $this->labels['single']['fizz']);

        $inc($multi, [9], $this->labels['multi']['one']);
        $dec($multi, [1, 1], $this->labels['multi']['one']);
        $inc($multi, [4, 5, 6], $this->labels['multi']['foo']);
        $dec($multi, [3, 4], $this->labels['multi']['foo']);
        $inc($multi, [3, 1, 1], $this->labels['multi']['beep']);
        $dec($multi, [2, 2], $this->labels['multi']['beep']);

        $this->compare('gauges');
    }

    /** @test */
    public function it_should_set_gauges_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelGaugeStub);
        $multi = $this->registry->register(new MultipleLabelsGaugeStub);

        $set = function (Gauge $gauge, array $values, array $labels) {
            foreach ($values as $value) {
                $gauge->set($value, $labels);
            }
        };

        $set($single, [5], $this->labels['single']['hello']);
        $set($single, [400, 7], $this->labels['single']['lorem']);
        $set($single, [2], $this->labels['single']['fizz']);

        $set($multi, [-20, 7], $this->labels['multi']['one']);
        $set($multi, [8], $this->labels['multi']['foo']);
        $set($multi, [0, 1], $this->labels['multi']['beep']);

        $this->compare('gauges');
    }

    /** @test */
    public function it_should_observe_histograms_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelHistogramStub);
        $multi = $this->registry->register(new MultipleLabelsHistogramStub);

        $observe = function (Histogram $histogram, array $values, array $labels) {
            foreach ($values as $value) {
                $histogram->observe($value, $labels);
            }
        };

        $observe($single, [99, 210, 234], $this->labels['single']['hello']);
        $observe($single, [399, 999, 400, 1111], $this->labels['single']['lorem']);
        $observe($single, [777, 700, 650, 600, 123], $this->labels['single']['fizz']);

        $observe($multi, [50, 850, 450, 650, 250], $this->labels['multi']['one']);
        $observe($multi, [900, 1000], $this->labels['multi']['foo']);
        $observe($multi, [777, 666, 555], $this->labels['multi']['beep']);

        $this->compare('histograms');
    }

    /** @test */
    public function it_should_observe_summaries_and_render_their_samples()
    {
        $single = $this->registry->register(new SingleLabelSummaryStub);
        $multi = $this->registry->register(new MultipleLabelsSummaryStub);

        $observe = function (Summary $summary, array $values, array $labels) {
            foreach ($values as $value) {
                $summary->observe($value, $labels);
            }
        };

        $observe($single, [5, 0, -4, -2, 1, -1, -1, 2, 4], $this->labels['single']['hello']);
        $observe($single, [-5, -12, -8, 6, -5, 1, 13, -7, -5, 5], $this->labels['single']['lorem']);
        $observe($single, [-8, -5, -17, 1, 21, 19, -18, -9, -25, -7, 25], $this->labels['single']['fizz']);

        $observe($multi, [-13, -12, -10, -5, 3, -13, 5, 8, 10], $this->labels['multi']['one']);
        $observe($multi, [18, 0, 13, 6, -5, -13, 9, -9, 11, 19], $this->labels['multi']['foo']);
        $observe($multi, [18, 4, -30, -30, -12, -27, 30, -4, -24, 12, -16], $this->labels['multi']['beep']);

        $this->compare('summaries');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$repository->flush();
    }

    /**
     * @param string $sample
     */
    private function compare(string $sample)
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/samples/{$sample}.txt"),
            (new TextRenderer)->render($this->registry->collect())
        );
    }
}
