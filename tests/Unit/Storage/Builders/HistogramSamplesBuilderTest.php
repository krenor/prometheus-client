<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Builders;

use ReflectionProperty;
use InvalidArgumentException;
use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub;
use Krenor\Prometheus\Storage\Builders\HistogramSamplesBuilder;

class HistogramSamplesBuilderTest extends TestCase
{
    /**
     * @test
     *
     * @group builders
     * @group exceptions
     * @group histograms
     */
    public function it_should_raise_an_exception_if_the_last_stored_element_contains_a_key_named_bucket()
    {
        $histogram = new SingleLabelHistogramStub;

        $labels = $histogram
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $builder = new HistogramSamplesBuilder($histogram, new Collection([
            "{\"labels\":{$labels},\"bucket\":100}" => 1,
        ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sum of bucket observations missing.');

        $builder->samples();
    }

    /**
     * @test
     *
     * @group builders
     * @group histograms
     */
    public function it_should_not_create_samples_for_buckets_which_are_not_present_on_the_histogram()
    {
        $histogram = new SingleLabelHistogramStub;

        $labels = $histogram
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $builder = new HistogramSamplesBuilder($histogram, new Collection([
            "{\"labels\":{$labels},\"bucket\":42}"   => 10,
            "{\"labels\":{$labels},\"bucket\":1337}" => 15,
            "{\"labels\":{$labels}}"                 => 0,
        ]));

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertNotEmpty($samples);
        $this->assertNull($samples->first(function (Sample $sample) {
            return $sample->labels()->get('le') === 42;
        }));
        $this->assertNull($samples->first(function (Sample $sample) {
            return $sample->labels()->get('le') === 1337;
        }));
    }

    /**
     * @test
     *
     * @group builders
     * @group histograms
     */
    public function it_should_create_histogram_samples()
    {
        $histogram = new SingleLabelHistogramStub;

        $labels = $histogram
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $builder = new HistogramSamplesBuilder($histogram, new Collection([
            "{\"labels\":{$labels},\"bucket\":150}"      => 2,
            "{\"labels\":{$labels},\"bucket\":600}"      => 5,
            "{\"labels\":{$labels},\"bucket\":\"+Inf\"}" => 7,
            "{\"labels\":{$labels}}"                     => 42,
        ]));

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount($histogram->buckets()->count() + 3, $samples);

        $this->assertContains('_bucket', $samples[0]->name());
        $this->assertEquals(100, $samples[0]->labels()->get('le'));
        // Only needs to match for the first one since the following ones reuse them
        $this->assertSame($labels, $samples[0]->labels()->except('le')->toJson());
        $this->assertEquals(0, $samples[0]->value());

        $this->assertContains('_bucket', $samples[1]->name());
        $this->assertEquals(150, $samples[1]->labels()->get('le'));
        $this->assertEquals(2, $samples[1]->value());

        $this->assertContains('_bucket', $samples[2]->name());
        $this->assertEquals(250, $samples[2]->labels()->get('le'));
        $this->assertEquals(2, $samples[2]->value());

        $this->assertContains('_bucket', $samples[3]->name());
        $this->assertEquals(400, $samples[3]->labels()->get('le'));
        $this->assertEquals(2, $samples[3]->value());

        $this->assertContains('_bucket', $samples[4]->name());
        $this->assertEquals(600, $samples[4]->labels()->get('le'));
        $this->assertEquals(7, $samples[4]->value());

        $this->assertContains('_bucket', $samples[5]->name());
        $this->assertEquals(850, $samples[5]->labels()->get('le'));
        $this->assertEquals(7, $samples[5]->value());

        $this->assertContains('_bucket', $samples[6]->name());
        $this->assertEquals('+Inf', $samples[6]->labels()->get('le'));
        $this->assertEquals(14, $samples[6]->value());

        $this->assertContains('_count', $samples[7]->name());
        $this->assertNull($samples[7]->labels()->get('le'));
        $this->assertSame($samples[6]->value(), $samples[7]->value());

        $this->assertContains('_sum', $samples[8]->name());
        $this->assertNull($samples[8]->labels()->get('le'));
        $this->assertEquals(42, $samples[8]->value());
    }

    /**
     * @test
     *
     * @group builders
     * @group histograms
     */
    public function it_should_initialize_histograms_without_labels()
    {
        $histogram = new SingleLabelHistogramStub;

        $reflection = new ReflectionProperty($histogram, 'labels');
        $reflection->setAccessible(true);
        $reflection->setValue($histogram, []);

        $builder = new HistogramSamplesBuilder($histogram, new Collection);

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount($histogram->buckets()->count() + 3, $samples);

        $this->assertEquals(0, $samples[0]->value()); // 100 Bucket
        // Only needs to match for the first one since the following ones reuse them
        $this->assertEmpty($samples[0]->labels()->except('le'));
        $this->assertEquals(0, $samples[1]->value()); // 150 Bucket
        $this->assertEquals(0, $samples[2]->value()); // 250 Bucket
        $this->assertEquals(0, $samples[3]->value()); // 400 Bucket
        $this->assertEquals(0, $samples[4]->value()); // 650 Bucket
        $this->assertEquals(0, $samples[5]->value()); // 850 Bucket
        $this->assertEquals(0, $samples[6]->value()); // Inf Bucket
        $this->assertEquals(0, $samples[7]->value()); // Count
        $this->assertEquals(0, $samples[8]->value()); // Sum
    }
}
