<?php

namespace Krenor\Prometheus\Tests\Stubs;

use Krenor\Prometheus\Metrics\Counter;

class MultipleLabelsCounterStub extends Counter
{
    /**
     * @var string
     */
    protected $namespace = 'example';

    /**
     * @var string
     */
    protected $name = 'multi_labeled_counter';

    /**
     * @var string
     */
    protected $description = 'Example Counter using multiple labels.';

    /**
     * @var string[]
     */
    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}
