Gauges
======

## Introduction

A gauge is a metric that represents a single numerical value that can arbitrarily go up and down.  
Examples include numbers of items in a queue, temperature or current memory usage. 

## Properties

As gauges extend the [abstract Metric](README.md) they offer the same [properties](README.md#properties).

## Methods

As gauges extend the [abstract Metric](README.md) they offer the same [methods](README.md#methods).  
Additionally they offer following functionality:

#### `increment(array $labels = [])`

Alias for [`incrementBy(1, $labels)`](#incrementbyfloat-value-array-labels--)

#### `incrementBy(float $value, array $labels = [])`

Pass a call to the underlying [Storage][storage-docs] to increment this gauge by `$value` with given **label values**

#### `decrement(array $labels = [])`

Alias for [`decrementBy(1, $labels)`](#decrementbyfloat-value-array-labels--)

#### `decrementBy(float $value, array $labels = [])`

Pass a call to the underlying [Storage][storage-docs] to decrement this gauge by `$value` with given **label values**

#### `set(float $value, array $labels = [])`

Pass a call to the underlying [Storage][storage-docs] to set this gauge to `$value` with given **label values**

#### `chronometer(array $labels = [], int $precision = 4)`

[Returns a closure to track execution time](TRACKING_EXECUTION_TIME.md)

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Gauge;

$gauge = new class extends Gauge {
    protected ?string $namespace = 'example';
    
    protected string $name = 'gauge';

    protected string $description = 'Example Gauge.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}

$gauge->increment(['foo', 'bar', 'baz']);
$gauge->decrement(['foo', 'bar', 'baz']);
$gauge->set(42, ['foo', 'bar', 'baz']);
``` 

[storage-docs]: ../storage/README.md
