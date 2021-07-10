<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Builders;

use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;
use Krenor\Prometheus\Storage\Builders\CounterSamplesBuilder;

class CounterSamplesBuilderTest extends TestCase
{
    /**
     * @test
     *
     * @group builders
     * @group counters
     */
    public function it_should_create_counter_samples()
    {
        $counter = new SingleLabelCounterStub;

        $labels = $counter
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $builder = new CounterSamplesBuilder($counter, new Collection([
            "{\"labels\":{$labels}}" => 42,
        ]));

        $samples = $builder->samples();

        $this->assertCount(1, $samples);
        $this->assertSame($labels, $samples[0]->labels()->toJson());
        $this->assertEquals(42, $samples[0]->value());
    }

    /**
     * @test
     *
     * @group builders
     * @group counters
     */
    public function it_should_initialize_counters_without_labels()
    {
        $counter = new SingleLabelCounterStub;

        $reflection = new ReflectionProperty($counter, 'labels');
        $reflection->setAccessible(true);
        $reflection->setValue($counter, []);

        $builder = new CounterSamplesBuilder($counter, new Collection);

        $samples = $builder->samples();

        $this->assertCount(1, $samples);
        $this->assertEmpty($samples[0]->labels());
        $this->assertEquals(0, $samples[0]->value());
    }
}
