<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Collectors;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\SamplesCollectorException;
use Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsHistogramStub;
use Krenor\Prometheus\Storage\Collectors\HistogramSamplesCollector;

class HistogramSamplesCollectorTest extends TestCase
{
    /**
     * @test
     *
     * @group collectors
     * @group histograms
     */
    public function it_should_collect_histogram_samples()
    {
        $metric = new SingleLabelHistogramStub;

        $labels = $metric->labels()->combine(['hello world'])->toJson();

        /** @var Sample[] $samples */
        $samples = (new HistogramSamplesCollector($metric, new Collection([
            "{\"labels\":{$labels},\"bucket\":150}"      => 1,
            "{\"labels\":{$labels},\"bucket\":600}"      => 2,
            "{\"labels\":{$labels},\"bucket\":\"+Inf\"}" => 3,
            "{\"labels\":{$labels}}"                     => 42,
        ])))->collect();

        $this->assertCount(9, $samples);
        $this->assertEquals(0, $samples[0]->value()); // 100 Bucket
        $this->assertEquals(1, $samples[1]->value()); // 150 Bucket
        $this->assertEquals(1, $samples[2]->value()); // 250 Bucket
        $this->assertEquals(1, $samples[3]->value()); // 400 Bucket
        $this->assertEquals(3, $samples[4]->value()); // 600 Bucket
        $this->assertEquals(3, $samples[5]->value()); // 850 Bucket
        $this->assertEquals(6, $samples[6]->value()); // +Inf Bucket
        $this->assertSame($samples[6]->value(), $samples[7]->value()); // Count
        $this->assertEquals(42, $samples[8]->value()); // Sum
    }

    /**
     * @test
     *
     * @group collectors
     * @group exceptions
     * @group histograms
     */
    public function it_should_raise_an_exception_if_the_last_stored_element_contains_the_key_bucket()
    {
        $metric = new SingleLabelHistogramStub;

        $labels = $metric->labels()->combine(['hello world'])->toJson();

        $this->expectException(SamplesCollectorException::class);
        $this->expectExceptionMessage('The last element has to be the sum of all bucket values.');

        (new HistogramSamplesCollector($metric, new Collection([
            "{\"labels\":{$labels},\"bucket\":100}" => 2,
        ])))->collect();
    }

    /**
     * @test
     *
     * @group collectors
     * @group histograms
     */
    public function it_should_silently_reject_stored_buckets_which_are_not_present_in_the_metric()
    {
        $metric = new MultipleLabelsHistogramStub;

        $labels = $metric->labels()->combine(['foo', 'bar', 'baz'])->toJson();

        /** @var Sample[] $samples */
        $samples = (new HistogramSamplesCollector($metric, new Collection([
            "{\"labels\":{$labels},\"bucket\":42}"   => 10,
            "{\"labels\":{$labels},\"bucket\":1337}" => 15,
            "{\"labels\":{$labels},\"bucket\":200}"  => 3,
            "{\"labels\":{$labels}}"                 => 100,
        ])))->collect();

        $this->assertCount(6, $samples);
        $this->assertSame($samples[0]->value(), $samples[3]->value()); // Compare 200 and +Inf buckets
    }
}
