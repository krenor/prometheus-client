Counters
========

## Introduction

A counter is a cumulative metric that represents a single numerical value that only ever goes up.  
A counter is typically used to count requests served, tasks completed, errors occurred, etc.  
Counters should not be used to expose current counts of items whose number can also go down  
as [gauges](GAUGES.md) are better suited for this.

## Properties

As counters extend the [Metric class](README.md) they offer the same properties.

## Methods

As counters extend the [Metric class](README.md) they offer the same methods.  
Additionally they offer following functionality:

#### `increment(array $labels)`

Alias for [`incrementBy(1, $labels)`](#incrementby(float-$value,-array-$labels)).

#### `incrementBy(float $value, array $labels)`

Pass a call to the [Storage](#) to increment this counter by `$value` with the given **label values**.

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
```
