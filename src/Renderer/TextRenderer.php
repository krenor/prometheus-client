<?php

namespace Krenor\Prometheus\Renderer;

use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\MetricFamilySamples;
use Krenor\Prometheus\Contracts\Renderable;

class TextRenderer implements Renderable
{
    /**
     * {@inheritdoc}
     */
    public function render(Collection $metrics): string
    {
        $text = $metrics->flatMap(function (MetricFamilySamples $family) {
            return $this->transform($family);
        })->implode("\n");

        return "{$text}\n";
    }

    /**
     * @param Collection $labels
     *
     * @return string
     */
    protected function serialize(Collection $labels): string
    {
        $quoted = $labels->map(function (string $value) {
            return "\"{$value}\"";
        });

        return urldecode(
            http_build_query($quoted->toArray(), '', ',')
        );
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

        $metrics = $family->samples()->map(function (Sample $sample) {
            $labels = $this->serialize($sample->labels());

            return "{$sample->name()}{{$labels}} {$sample->value()}";
        })->values();

        return $lines->merge($metrics);
    }
}
