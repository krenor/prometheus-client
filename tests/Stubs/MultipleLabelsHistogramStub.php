<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Histogram;

class MultipleLabelsHistogramStub extends Histogram
{
    protected string $namespace = 'example';

    protected string $name = 'multi_labeled_histogram';

    protected string $description = 'Example Histogram using multiple labels.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    protected array $buckets = [
        200,
        400,
        600,
    ];
}
