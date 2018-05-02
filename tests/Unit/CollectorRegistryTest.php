<?php

namespace Krenor\Prometheus\Tests\Unit;

use Mockery as m;
use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Contracts\Storage;
use Krenor\Prometheus\CollectorRegistry;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsGaugeStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsCounterStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsSummaryStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsHistogramStub;

class CollectorRegistryTest extends TestCase
{
    /**
     * @var CollectorRegistry
     */
    private $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->registry = new CollectorRegistry;
    }

    /**
     * @test
     *
     * @group registry
     */
    public function it_should_register_metrics_and_make_them_accessible_via_its_collector()
    {
        $counter = new MultipleLabelsCounterStub;
        $gauge = new MultipleLabelsGaugeStub;
        $histogram = new MultipleLabelsHistogramStub;
        $summary = new MultipleLabelsSummaryStub;

        $this->assertSame($counter, $this->registry->register($counter));
        $this->assertSame($gauge, $this->registry->register($gauge));
        $this->assertSame($histogram, $this->registry->register($histogram));
        $this->assertSame($summary, $this->registry->register($summary));

        $this->assertSame($counter, $this->registry->counter(MultipleLabelsCounterStub::class));
        $this->assertSame($gauge, $this->registry->gauge(MultipleLabelsGaugeStub::class));
        $this->assertSame($histogram, $this->registry->histogram(MultipleLabelsHistogramStub::class));
        $this->assertSame($summary, $this->registry->summary(MultipleLabelsSummaryStub::class));

        $this->assertCount(1, $this->registry->counters());
        $this->assertCount(1, $this->registry->gauges());
        $this->assertCount(1, $this->registry->histograms());
        $this->assertCount(1, $this->registry->summaries());
    }

    /**
     * @test
     *
     * @group registry
     */
    public function it_should_unregister_metrics_and_remove_them_from_their_collector()
    {
        $metric = new MultipleLabelsGaugeStub;

        $this->assertSame($metric, $this->registry->register($metric));
        $this->assertCount(1, $this->registry->gauges());

        $this->registry->unregister($metric);

        $this->assertNull($this->registry->gauge(MultipleLabelsGaugeStub::class));
        $this->assertCount(0, $this->registry->gauges());
    }

    /**
     * @test
     *
     * @group registry
     */
    public function it_should_collect_all_registered_metric_samples()
    {
        $storage = m::mock(Storage::class);

        Metric::storeUsing($storage);

        $metrics = [
            new MultipleLabelsCounterStub,
            new MultipleLabelsGaugeStub,
            new MultipleLabelsHistogramStub,
            new MultipleLabelsSummaryStub,
        ];

        $this->registry->register($metrics[0]);
        $this->registry->register($metrics[1]);
        $this->registry->register($metrics[2]);
        $this->registry->register($metrics[3]);

        $sample = function (string $name) {
            return new Collection([
                new Sample($name, 1, new Collection),
            ]);
        };

        $storage->expects('collect')
                ->once()
                ->with(MultipleLabelsCounterStub::class)
                ->andReturn($sample('counter_sample'));

        $storage->expects('collect')
                ->once()
                ->with(MultipleLabelsGaugeStub::class)
                ->andReturn($sample('gauge_sample'));

        $storage->expects('collect')
                ->once()
                ->with(MultipleLabelsHistogramStub::class)
                ->andReturn($sample('histogram_sample'));

        $storage->expects('collect')
                ->once()
                ->with(MultipleLabelsSummaryStub::class)
                ->andReturn($sample('summary_sample'));

        $collection = $this->registry->collect();

        $this->assertCount(4, $collection);

        /** @var MetricFamilySamples $family */
        foreach ($collection as $index => $family) {
            $this->assertEquals($metrics[$index], $family->metric());
            $this->assertCount(1, $family->samples());
        }
    }
}
