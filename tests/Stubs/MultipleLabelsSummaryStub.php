<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Summary;

class MultipleLabelsSummaryStub extends Summary
{
    protected ?string $namespace = 'example';

    protected string $name = 'multi_labeled_summary';

    protected string $description = 'Example Summary using multiple labels.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    protected array $quantiles = [
        .1,
        .3,
        .5,
        .7,
        .9,
    ];
}
