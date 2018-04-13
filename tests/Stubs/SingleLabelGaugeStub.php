<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Gauge;

class SingleLabelGaugeStub extends Gauge
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'gauge';

    /**
     * @var string
     */
    protected $description = 'Example Gauge.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
    ];
}
