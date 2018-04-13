<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Gauge;

class MultipleLabelsGaugeStub extends Gauge
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'multi_labeled_gauge';

    /**
     * @var string
     */
    protected $description = 'Example Gauge using multiple labels.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}
