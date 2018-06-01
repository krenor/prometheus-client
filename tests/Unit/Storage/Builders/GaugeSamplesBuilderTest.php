<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Builders;

use ReflectionProperty;
use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub;
use Krenor\Prometheus\Storage\Builders\GaugeSamplesBuilder;

class GaugeSamplesBuilderTest extends TestCase
{
    /**
     * @test
     *
     * @group builders
     * @group gauges
     */
    public function it_should_create_gauge_samples()
    {
        $gauge = new SingleLabelGaugeStub;

        $labels = $gauge
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $builder = new GaugeSamplesBuilder($gauge, new Collection([
            "{\"labels\":{$labels}}" => 99,
        ]));

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount(1, $samples);

        $this->assertSame($labels, $samples[0]->labels()->toJson());
        $this->assertEquals(99, $samples[0]->value());
    }

    /**
     * @test
     *
     * @group builders
     * @group gauges
     */
    public function it_should_initialize_gauges_without_labels()
    {
        $gauge = new SingleLabelGaugeStub;

        $reflection = new ReflectionProperty($gauge, 'labels');
        $reflection->setAccessible(true);
        $reflection->setValue($gauge, []);

        $builder = new GaugeSamplesBuilder($gauge, new Collection);

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount(1, $samples);

        $this->assertEmpty($samples[0]->labels());
        $this->assertEquals(0, $samples[0]->value());
    }
}
