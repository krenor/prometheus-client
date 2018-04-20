<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Collectors;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;
use Krenor\Prometheus\Storage\Collectors\SamplesCollector;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsGaugeStub;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsCounterStub;

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
        $metric = new MultipleLabelsCounterStub;

        $labels = [
            $metric->labels()->combine(['foo', 'bar', 'baz']),
            $metric->labels()->combine(['bar', 'baz', 'foo']),
            $metric->labels()->combine(['baz', 'foo', 'bar']),
        ];

        $values = [2, 5, 7];

        /** @var Sample[] $sample */
        $samples = (new SamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]->toJson()}}" => $values[0],
            "{\"labels\":{$labels[1]->toJson()}}" => $values[1],
            "{\"labels\":{$labels[2]->toJson()}}" => $values[2],
        ])))->collect();

        $this->assertCount(3, $samples);

        foreach ($samples as $index => $sample) {
            $this->assertSame("{$metric->namespace()}_{$metric->name()}", $sample->name());
            $this->assertEquals($labels[$index], $sample->labels());
            $this->assertEquals($values[$index], $sample->value());
        }
    }

    /**
     * @test
     *
     * @group collectors
     * @group gauges
     */
    public function it_should_collect_gauge_samples()
    {
        $metric = new MultipleLabelsGaugeStub;

        $labels = [
            $metric->labels()->combine(['one', 'two', 'three']),
            $metric->labels()->combine(['two', 'three', 'one']),
            $metric->labels()->combine(['three', 'one', 'two']),
        ];

        $values = [3, 1, 4];

        /** @var Sample[] $sample */
        $samples = (new SamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]->toJson()}}" => $values[0],
            "{\"labels\":{$labels[1]->toJson()}}" => $values[1],
            "{\"labels\":{$labels[2]->toJson()}}" => $values[2],
        ])))->collect();

        $this->assertCount(3, $samples);

        foreach ($samples as $index => $sample) {
            $this->assertSame("{$metric->namespace()}_{$metric->name()}", $sample->name());
            $this->assertEquals($labels[$index], $sample->labels());
            $this->assertEquals($values[$index], $sample->value());
        }
    }

    /**
     * @test
     *
     * @group collectors
     */
    public function it_should_silently_reject_data_which_lacks_the_labels_key()
    {
        $multi = new SingleLabelCounterStub;

        $labels = $multi->labels()->combine(['foo'])->toJson();

        /** @var Sample[] $samples */
        $samples = (new SamplesCollector($multi, new Collection([
            "{\"foo\":{$labels}}"    => 2,
            "{\"bar\":{$labels}}"    => 3,
            "{\"baz\":{$labels}}"    => 4,
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

        $labels = [
            $single->labels()->combine(['hello world']),
            $multi->labels()->combine(['foo', 'bar', 'baz']),
            $single->labels()->combine(['world, hello']),
        ];

        /** @var Sample[] $samples */
        $samples = (new SamplesCollector($multi, new Collection([
            "{\"labels\":{$labels[0]->toJson()}}" => 1,
            "{\"labels\":{$labels[1]->toJson()}}" => 2,
            "{\"labels\":{$labels[2]->toJson()}}" => 3,
        ])))->collect();

        $this->assertCount(1, $samples);
    }
}
