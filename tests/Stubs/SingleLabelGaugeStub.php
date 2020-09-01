<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Gauge;

class SingleLabelGaugeStub extends Gauge
{
    protected ?string $namespace = 'example';

    protected string $name = 'gauge';

    protected string $description = 'Example Gauge.';

    protected array $labels = [
        'example_label',
    ];
}
