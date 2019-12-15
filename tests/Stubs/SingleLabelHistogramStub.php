<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Histogram;

class SingleLabelHistogramStub extends Histogram
{
    protected string $namespace = 'example';

    protected string $name = 'histogram';

    protected string $description = 'Example Histogram.';

    protected array $labels = [
        'example_label',
    ];

    protected array $buckets = [
        100,
        150,
        250,
        400,
        600,
        850,
    ];
}
