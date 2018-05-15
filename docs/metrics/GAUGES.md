Gauges
======

## Introduction

A gauge is a metric that represents a single numerical value that can arbitrarily go up and down.  
Examples include numbers of items in a queue, temperature or current memory usage. 

## Properties

As gauges extend the [Metric class](README.md) they offer the same properties.

## Methods

As gauges extend the [Metric class](README.md) they offer the same methods.  
Additionally they offer following functionality:

#### `increment(array $labels)`

Alias for [`incrementBy(1, $labels)`](#incrementby(float-$value,-array-$labels)).

#### `incrementBy(float $value, array $labels)`

Pass a call to the [Storage][storage-docs] to increment this gauge by `$value` with the given **label values**.

#### `decrement(array $labels)`

Alias for [`decrementBy(1, $labels)`](#decrementby(float-$value,-array-$labels)).

#### `decrementBy(float $value, array $labels)`

Pass a call to the [Storage][storage-docs] to decrement this gauge by `$value` with the given **label values**.

#### `set(float $value, array $labels)`

Pass a call to the [Storage][storage-docs] to set this gauge to the given `$value` with the given **label values**.

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Gauge;

$gauge = new class extends Gauge {
    protected $namespace = 'example';
    
    protected $name = 'gauge';

    protected $description = 'Example Gauge.';

    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}
``` 

[storage-docs]: ../storage/README.md
