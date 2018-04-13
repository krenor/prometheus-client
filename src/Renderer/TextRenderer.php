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
    public function render(Collection $metrics)
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

    protected function transform(MetricFamilySamples $family)
    {
        $metric = $family->metric();
        $name = $family->samples()->first()->name();

        $lines = new Collection([
            "# HELP {$name} {$metric->description()}",
            "# TYPE {$name} {$metric->type()}",
        ]);

        $metrics = $family->samples()->map(function (Sample $sample) use ($metric, $name) {
            $labels = $this->serialize($sample->labels());

            if ($name !== $sample->name()) {
                $breakpoint = true;
            }

            return "{$name}{{$labels}} {$sample->value()}";
        })->values();

        return $lines->merge($metrics);
    }
}
