<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Counter;

class MultipleLabelsCounterStub extends Counter
{
    protected ?string $namespace = 'example';

    protected string $name = 'multi_labeled_counter';

    protected string $description = 'Example Counter using multiple labels.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}
