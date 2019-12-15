<?php

namespace Krenor\Prometheus\Renderer;

use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;
use Krenor\Prometheus\Contracts\Renderable;

class TextRenderer implements Renderable
{
    const CONTENT_TYPE = 'text/plain; version=0.0.4';

    /**
     * {@inheritdoc}
     */
    public function render(Collection $metrics): string
    {
        return $metrics
            ->flatMap(fn(MetricFamilySamples $family) => $this->transform($family))
            ->implode(PHP_EOL) . PHP_EOL;
    }

    /**
     * @param Collection $labels
     *
     * @return string
     */
    protected function serialize(Collection $labels): string
    {
        if ($labels->isEmpty()) {
            return '';
        }

        $quoted = $labels->map(fn(string $value) => "\"{$value}\"");

        $serialized = urldecode(
            http_build_query($quoted->toArray(), '', ',')
        );

        return "{{$serialized}}";
    }

    /**
     * @param MetricFamilySamples $family
     *
     * @return Collection
     */
    protected function transform(MetricFamilySamples $family): Collection
    {
        $metric = $family->metric();

        $lines = new Collection([
            "# HELP {$metric->key()} {$metric->description()}",
            "# TYPE {$metric->key()} {$metric->type()}",
        ]);

        $metrics = $family
            ->samples()
            ->map(fn(Sample $sample) => "{$sample->name()}{$this->serialize($sample->labels())} {$sample->value()}")
            ->values();

        return $lines->merge($metrics);
    }
}
