<?php

namespace Krenor\Prometheus\Tests\Unit\Renderer;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;
use Krenor\Prometheus\Renderer\TextRenderer;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsCounterStub;

class TextRendererTest extends TestCase
{
    /**
     * @test
     *
     * @group renderer
     */
    public function it_should_render_metric_family_samples()
    {
        $metric = new MultipleLabelsCounterStub;
        $identifier = $metric->key();

        $samples = new MetricFamilySamples($metric, new Collection([
            new Sample($identifier, 2, $metric->labels()->combine(['foo', 'bar', 'baz'])),
            new Sample($identifier, 5, $metric->labels()->combine(['one', 'two', 'three'])),
            new Sample($identifier, 11, $metric->labels()->combine(['lorem', 'ipsum', 'dolor'])),
        ]));

        $expected = (new Collection)
            ->push("# HELP {$identifier} {$metric->description()}")
            ->push("# TYPE {$identifier} {$metric->type()}")
            ->push("{$identifier}{example_label=\"foo\",other_label=\"bar\",yet_another_label=\"baz\"} 2")
            ->push("{$identifier}{example_label=\"one\",other_label=\"two\",yet_another_label=\"three\"} 5")
            ->push("{$identifier}{example_label=\"lorem\",other_label=\"ipsum\",yet_another_label=\"dolor\"} 11")
            ->implode("\n");

        $this->assertSame("{$expected}\n", (new TextRenderer)->render(new Collection([$samples])));
    }
}
