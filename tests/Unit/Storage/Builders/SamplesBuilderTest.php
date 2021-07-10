<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Builders;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Contracts\Metric;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class SamplesBuilderTest extends TestCase
{
    /**
     * @var Metric|m\MockInterface
     */
    private $metric;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->metric = m::mock(Metric::class);

        $this->metric
            ->shouldReceive('key')
            ->zeroOrMoreTimes()
            ->andReturn('mocked_metric');
    }

    /**
     * @test
     *
     * @group builders
     */
    public function it_should_exclude_items_which_lack_the_labels_key()
    {
        $this->metric
            ->shouldReceive('labels')
            ->once()
            ->andReturn(new Collection);

        $labels = json_encode(['foo' => 'bar']);

        $items = new Collection([
            "{\"foo\":{$labels}}" => 1,
            "{\"bar\":{$labels}}" => 2,
            "{\"baz\":{$labels}}" => 3,
        ]);

        $builder = new class($this->metric, $items) extends SamplesBuilder {
            //
        };

        $this->assertCount(0, $builder->samples());
    }

    /**
     * @test
     *
     * @group builders
     */
    public function it_should_exclude_items_which_labels_names_differ_from_those_of_the_given_metric()
    {
        $this->metric
            ->shouldReceive('labels')
            ->once()
            ->andReturn(new Collection([
                'foo',
            ]));

        $labels = [
            json_encode(['foo' => 'bar']),
            json_encode(['bar' => 'baz']),
            json_encode(['hello' => 'world']),
        ];

        $items = new Collection([
            "{\"labels\":{$labels[0]}}" => 1,
            "{\"labels\":{$labels[1]}}" => 2,
            "{\"labels\":{$labels[2]}}" => 3,
        ]);

        $builder = new class($this->metric, $items) extends SamplesBuilder {
            //
        };

        $samples = $builder->samples();

        $this->assertCount(1, $samples);
        $this->assertSame($labels[0], $samples[0]->labels()->toJson());
    }

    /**
     * @test
     *
     * @group builders
     */
    public function it_create_samples_ordered_by_their_labels()
    {
        $labelNames = new Collection([
            'hello',
        ]);

        $this->metric
            ->shouldReceive('labels')
            ->once()
            ->andReturn($labelNames);

        $labels = [
            $labelNames->combine(['ma honey'])->toJson(),
            $labelNames->combine(['world'])->toJson(),
            $labelNames->combine(['ma ragtime gal'])->toJson(),
            $labelNames->combine(['ma baby'])->toJson(),
        ];

        $items = new Collection([
            "{\"labels\":{$labels[0]}}" => 10,
            "{\"labels\":{$labels[1]}}" => 20,
            "{\"labels\":{$labels[2]}}" => 30,
            "{\"labels\":{$labels[3]}}" => 40,
        ]);

        $builder = new class($this->metric, $items) extends SamplesBuilder {
            //
        };

        $samples = $builder->samples();

        $this->assertCount(4, $samples);
        $this->assertSame($labels[3], $samples[0]->labels()->toJson()); // You know
        $this->assertSame($labels[0], $samples[1]->labels()->toJson()); // how the
        $this->assertSame($labels[2], $samples[2]->labels()->toJson()); // song goes :-)
        $this->assertSame($labels[1], $samples[3]->labels()->toJson());
    }
}
