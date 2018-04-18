<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Summary;

class MultipleLabelsSummaryStub extends Summary
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'multi_labeled_summary';

    /**
     * @var string
     */
    protected $description = 'Example Summary using multiple labels.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    /**
     * @var int[]
     */
    protected $quantiles = [
        .1,
        .3,
        .5,
        .7,
        .9,
    ];
}
