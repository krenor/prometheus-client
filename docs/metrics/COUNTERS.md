Counters
========

## Introduction

A counter is a cumulative metric that represents a single numerical value that only ever goes up.  
A counter is typically used to count requests served, tasks completed, errors occurred, etc.  
Counters should not be used to expose current counts of items whose number can also go down  
as [gauges](GAUGES.md) are better suited for this.

## Properties

As counters extend the [abstract Metric](README.md) they offer the same [properties](README.md#properties).

## Methods

As counters extend the [abstract Metric](README.md) they offer the same [methods](README.md#methods).  
Additionally they offer following functionality:

#### `increment(array $labels = [])`

Alias for [`incrementBy(1, $labels)`](#incrementbyfloat-value-array-labels--)

#### `incrementBy(float $value, array $labels = [])`

Pass a call to the underlying [Storage](../storage/README.md) to increment this counter by `$value` 
with given **label values**.  
Incrementing by a negative amount will result in an exception.

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Counter;

$counter = new class extends Counter {
    protected $namespace = 'example';
    
    protected $name = 'counter';

    protected $description = 'Example Counter.';

    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];
}

$counter->increment(['foo', 'bar', 'baz']);
```
