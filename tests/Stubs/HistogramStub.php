<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Histogram;

class HistogramStub extends Histogram
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
        'example:histogram',
    ];

    /**
     * @var int[]
     */
    protected $buckets = [
        2,
        3,
        5,
        7,
    ];
}
