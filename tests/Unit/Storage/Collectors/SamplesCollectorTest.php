<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Collectors;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;
use Krenor\Prometheus\Storage\Collectors\SamplesCollector;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsGaugeStub;

class SamplesCollectorTest extends TestCase
{
    /**
     * @test
     *
     * @group collectors
     * @group counters
     */
    public function it_should_collect_counter_samples()
    {
        $metric = new SingleLabelCounterStub;

        $labels = [
            $metric->labels()->combine(['hello world'])->toJson(),
            $metric->labels()->combine(['lorem ipsum'])->toJson(),
            $metric->labels()->combine(['fizz buzz'])->toJson(),
        ];

        $values = [2, 5, 7];

        /** @var Sample[] $samples */
        $samples = (new SamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]}}" => $values[0],
            "{\"labels\":{$labels[1]}}" => $values[1],
            "{\"labels\":{$labels[2]}}" => $values[2],
        ])))->collect();

        $this->assertSame(count($labels), count($samples));

        // Labeled with ['fizz buzz']
        $this->assertSame($labels[2], $samples[0]->labels()->toJson());
        $this->assertEquals($values[2], $samples[0]->value());

        // Labeled with ['hello world']
        $this->assertSame($labels[0], $samples[1]->labels()->toJson());
        $this->assertEquals($values[0], $samples[1]->value());

        // Labeled with ['lorem ipsum']
        $this->assertSame($labels[1], $samples[2]->labels()->toJson());
        $this->assertEquals($values[1], $samples[2]->value());
    }

    /**
     * @test
     *
     * @group collectors
     * @group gauges
     */
    public function it_should_collect_gauge_samples()
    {
        $metric = new SingleLabelGaugeStub;

        $labels = [
            $metric->labels()->combine(['hello world'])->toJson(),
            $metric->labels()->combine(['lorem ipsum'])->toJson(),
            $metric->labels()->combine(['fizz buzz'])->toJson(),
        ];

        $values = [1, 8, 7];

        /** @var Sample[] $samples */
        $samples = (new SamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]}}" => $values[0],
            "{\"labels\":{$labels[1]}}" => $values[1],
            "{\"labels\":{$labels[2]}}" => $values[2],
        ])))->collect();

        $this->assertSame(count($labels), count($samples));

        // Labeled with ['fizz buzz']
        $this->assertSame($labels[2], $samples[0]->labels()->toJson());
        $this->assertEquals($values[2], $samples[0]->value());

        // Labeled with ['hello world']
        $this->assertSame($labels[0], $samples[1]->labels()->toJson());
        $this->assertEquals($values[0], $samples[1]->value());

        // Labeled with ['lorem ipsum']
        $this->assertSame($labels[1], $samples[2]->labels()->toJson());
        $this->assertEquals($values[1], $samples[2]->value());
    }

    /**
     * @test
     *
     * @group collectors
     */
    public function it_should_silently_reject_data_which_lacks_the_labels_key()
    {
        $metric = new SingleLabelCounterStub;

        $labels = $metric->labels()->combine(['foo'])->toJson();

        $samples = (new SamplesCollector($metric, new Collection([
            "{\"foo\":{$labels}}" => 1,
            "{\"bar\":{$labels}}" => 2,
            "{\"baz\":{$labels}}" => 3,
        ])))->collect();

        $this->assertCount(0, $samples);
    }

    /**
     * @test
     *
     * @group collectors
     */
    public function it_should_silently_reject_data_which_label_keys_do_not_match_these_of_the_metric()
    {
        $multi = new MultipleLabelsGaugeStub;
        $single = new SingleLabelGaugeStub;

        $labels = new Collection([
            $single->labels()->combine(['hello world'])->toJson(),
            $multi->labels()->combine(['foo', 'bar', 'baz'])->toJson(),
            $single->labels()->combine(['lorem ipsum'])->toJson(),
        ]);

        $values = [1, 2, 3];

        /** @var Sample[] $samples */
        $samples = (new SamplesCollector($multi, new Collection([
            "{\"labels\":{$labels[0]}}" => $values[0],
            "{\"labels\":{$labels[1]}}" => $values[1],
            "{\"labels\":{$labels[2]}}" => $values[2],
        ])))->collect();

        $this->assertCount(1, $samples);
        $this->assertSame($labels[1], $samples[0]->labels()->toJson());
        $this->assertEquals($values[1], $samples[0]->value());
    }
}
