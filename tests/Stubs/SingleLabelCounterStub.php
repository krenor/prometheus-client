<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Counter;

class SingleLabelCounterStub extends Counter
{
    protected ?string $namespace = 'example';

    protected string $name = 'counter';

    protected string $description = 'Example Counter.';

    protected array $labels = [
        'example_label',
    ];
}
