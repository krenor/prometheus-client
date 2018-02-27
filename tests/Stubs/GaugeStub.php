<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Gauge;

class GaugeStub extends Gauge
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
        'example:gauge',
    ];
}
