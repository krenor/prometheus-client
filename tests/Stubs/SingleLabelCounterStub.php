<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Counter;

class SingleLabelCounterStub extends Counter
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'counter';

    /**
     * @var string
     */
    protected $description = 'Example Counter.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
    ];
}
