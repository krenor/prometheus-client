<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Histogram;

class MultipleLabelsHistogramStub extends Histogram
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'multi_labeled_histogram';

    /**
     * @var string
     */
    protected $description = 'Example Histogram using multiple labels.';

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
    protected $buckets = [
        200,
        400,
        600,
    ];
}
