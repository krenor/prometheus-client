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

        $labels = new Collection([
            $metric->labels()->combine(['hello world'])->toJson(),
            $metric->labels()->combine(['lorem ipsum'])->toJson(),
            $metric->labels()->combine(['fizz buzz'])->toJson(),
        ]);

        /** @var Sample[] $samples */
        $samples = (new HistogramSamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]},\"bucket\":150}"      => 2,
            "{\"labels\":{$labels[0]},\"bucket\":600}"      => 5,
            "{\"labels\":{$labels[0]},\"bucket\":\"+Inf\"}" => 7,
            "{\"labels\":{$labels[0]}}"                     => 42,

            "{\"labels\":{$labels[1]},\"bucket\":250}" => 3,
            "{\"labels\":{$labels[1]},\"bucket\":400}" => 1,
            "{\"labels\":{$labels[1]}}"                => 187,

            "{\"labels\":{$labels[2]},\"bucket\":\"+Inf\"}" => 8,
            "{\"labels\":{$labels[2]}}"                     => 11,
        ])))->collect();

        $this->assertCount(($metric->buckets()->count() + 3) * $labels->count(), $samples);

        // Labeled with ['fizz buzz']
        $this->assertSame($labels[2], $samples[0]->labels()->forget('le')->toJson());
        $this->assertEquals(0, $samples[0]->value()); // 100 Bucket
        $this->assertEquals(0, $samples[1]->value()); // 150 Bucket
        $this->assertEquals(0, $samples[2]->value()); // 250 Bucket
        $this->assertEquals(0, $samples[3]->value()); // 400 Bucket
        $this->assertEquals(0, $samples[4]->value()); // 600 Bucket
        $this->assertEquals(0, $samples[5]->value()); // 850 Bucket
        $this->assertEquals(8, $samples[6]->value()); // +Inf Bucket
        $this->assertSame($samples[6]->value(), $samples[7]->value()); // Count
        $this->assertEquals(11, $samples[8]->value()); // Sum

        // Labeled with ['hello world']
        $this->assertSame($labels[0], $samples[9]->labels()->forget('le')->toJson());
        $this->assertEquals(0, $samples[9]->value()); // 100 Bucket
        $this->assertEquals(2, $samples[10]->value()); // 150 Bucket
        $this->assertEquals(2, $samples[11]->value()); // 250 Bucket
        $this->assertEquals(2, $samples[12]->value()); // 400 Bucket
        $this->assertEquals(7, $samples[13]->value()); // 600 Bucket
        $this->assertEquals(7, $samples[14]->value()); // 850 Bucket
        $this->assertEquals(14, $samples[15]->value()); // +Inf Bucket
        $this->assertSame($samples[15]->value(), $samples[16]->value()); // Count
        $this->assertEquals(42, $samples[17]->value()); // Sum

        // Labeled with ['lorem ipsum']
        $this->assertSame($labels[1], $samples[18]->labels()->forget('le')->toJson());
        $this->assertEquals(0, $samples[18]->value()); // 100 Bucket
        $this->assertEquals(0, $samples[19]->value()); // 150 Bucket
        $this->assertEquals(3, $samples[20]->value()); // 250 Bucket
        $this->assertEquals(4, $samples[21]->value()); // 400 Bucket
        $this->assertEquals(4, $samples[22]->value()); // 600 Bucket
        $this->assertEquals(4, $samples[23]->value()); // 850 Bucket
        $this->assertEquals(4, $samples[24]->value()); // +Inf Bucket
        $this->assertSame($samples[24]->value(), $samples[25]->value()); // Count
        $this->assertEquals(187, $samples[26]->value()); // Sum
    }

    /**
     * @test
     *
     * @group collectors
     * @group exceptions
     * @group histograms
     */
    public function it_should_raise_an_exception_if_the_last_stored_element_contains_a_bucket_key()
    {
        $metric = new SingleLabelHistogramStub;

        $labels = $metric->labels()->combine(['hello world'])->toJson();

        $this->expectException(SamplesCollectorException::class);
        $this->expectExceptionMessage('The last element has to be the sum of all bucket values.');

        (new HistogramSamplesCollector($metric, new Collection([
            "{\"labels\":{$labels},\"bucket\":100}" => 1,
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

        $this->assertCount($metric->buckets()->count() + 3, $samples);
        $this->assertSame($samples[0]->value(), $samples[3]->value()); // 200 and +Inf Bucket
    }
}
