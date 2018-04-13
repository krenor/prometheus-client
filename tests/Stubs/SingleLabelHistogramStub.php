<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Histogram;

class SingleLabelHistogramStub extends Histogram
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'histogram';

    /**
     * @var string
     */
    protected $description = 'Example Histogram.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
    ];

    /**
     * @var int[]
     */
    protected $buckets = [
        100,
        150,
        250,
        400,
        600,
        850,
    ];
}
