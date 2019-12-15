<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Summary;

class SingleLabelSummaryStub extends Summary
{
    protected string $namespace = 'example';

    protected string $name = 'summary';

    protected string $description = 'Example Summary.';

    protected array $labels = [
        'example_label',
    ];

    protected array $quantiles = [
        .2,
        .4,
        .6,
        .8,
    ];
}
