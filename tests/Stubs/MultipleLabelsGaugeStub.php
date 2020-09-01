<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Gauge;

class MultipleLabelsGaugeStub extends Gauge
{
    protected ?string $namespace = 'example';

    protected string $name = 'multi_labeled_gauge';

    protected string $description = 'Example Gauge using multiple labels.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}
