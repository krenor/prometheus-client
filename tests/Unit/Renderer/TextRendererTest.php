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
        $renderer = new TextRenderer;
        $metric = new MultipleLabelsCounterStub;
        $identifier = $metric->key();

        $samples = new Collection([
            new Sample($identifier, 2, $metric->labels()->combine(['foo', 'bar', 'baz'])),
            new Sample($identifier, 5, $metric->labels()->combine(['one', 'two', 'three'])),
            new Sample($identifier, 11, $metric->labels()->combine(['lorem', 'ipsum', 'dolor'])),
        ]);

        $metrics = new Collection([
            new MetricFamilySamples($metric, $samples),
        ]);

        $expected = (new Collection)
            ->push("# HELP {$identifier} {$metric->description()}")
            ->push("# TYPE {$identifier} {$metric->type()}")
            ->push("{$identifier}{example_label=\"foo\",other_label=\"bar\",yet_another_label=\"baz\"} 2")
            ->push("{$identifier}{example_label=\"one\",other_label=\"two\",yet_another_label=\"three\"} 5")
            ->push("{$identifier}{example_label=\"lorem\",other_label=\"ipsum\",yet_another_label=\"dolor\"} 11")
            ->implode("\n");

        $this->assertSame("{$expected}\n", $renderer->render($metrics));
    }

    /**
     * @test
     *
     * @group renderer
     */
    public function it_should_render_metric_family_samples_containing_no_labels()
    {
        $renderer = new TextRenderer;
        $metric = new MultipleLabelsCounterStub;
        $identifier = $metric->key();

        $samples = new Collection([
            new Sample($identifier, 1.75, new Collection),
            new Sample($identifier, 5.25, new Collection),
            new Sample($identifier, 10.5, new Collection),
        ]);

        $metrics = new Collection([
            new MetricFamilySamples($metric, $samples),
        ]);

        $expected = (new Collection)
            ->push("# HELP {$identifier} {$metric->description()}")
            ->push("# TYPE {$identifier} {$metric->type()}")
            ->push("{$identifier} 1.75")
            ->push("{$identifier} 5.25")
            ->push("{$identifier} 10.5")
            ->implode("\n");

        $this->assertSame("{$expected}\n", $renderer->render($metrics));
    }
}
