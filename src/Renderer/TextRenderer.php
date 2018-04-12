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
            $metric = $family->metric();
            $identifier = "{$metric->namespace()}:{$metric->name()}";

            $lines = collect([
                "# HELP {$identifier} {$metric->description()}",
                "# TYPE {$identifier} {$metric->type()}",
            ]);

            $samples = $family->samples()->map(function (Sample $sample) use ($identifier) {
                $labels = $this->serialize($sample->data()->get('labels'));

                return "{$identifier}{{$labels}} {$sample->value()}";
            })->values();

            return $lines->merge($samples);
        })->implode("\n");

        return "{$text}\n";
    }

    /**
     * @param array $labels
     *
     * @return string
     */
    protected function serialize(array $labels): string
    {
        $quoted = array_map(function (string $value) {
            return "\"{$value}\"";
        }, $labels);

        return urldecode(
            http_build_query($quoted, '', ',')
        );
    }
}
