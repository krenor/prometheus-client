<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Summary;

class SingleLabelSummaryStub extends Summary
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'summary';

    /**
     * @var string
     */
    protected $description = 'Example Summary.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
    ];

    /**
     * @var int[]
     */
    protected $quantiles = [
        .2,
        .4,
        .6,
        .8,
    ];
}
