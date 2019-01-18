Collector Registry
==================

## Introduction

The `CollectorRegistry` is.. well, it's a collector registry. It collects samples of registered metrics.  
By default only the [supported metric](metrics/README.md#classes) are handled. Custom metrics require [a few additional steps](metrics/CUSTOM.md).

## Methods

#### `collect()`

Collects `Samples` collections of each registered metrics and maps them to `MetricFamilySamples`

#### `register(Metric $metric)`

Registers the metric by its namespace, if it's not already registered, and returning `$metric` back.  
Samples are collected only from registered metrics.

#### `unregister(Metric $metric)`

Unregisters the metric and therefore ignores it while collecting samples

**For each [default supported metric](metrics/README.md) there's an equally named plural/singular getter** to access either
* The collection of a metric type 
* A metric from said collection by its namespace

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\CollectorRegistry;

$registry = new CollectorRegistry;

/** @var Counter $counter */
$counter = $registry->register(new class extends Counter {
    // ...
});

$counters = $registry->counters();
$identical = $counter === $registry->counter(get_class($counter));

// Fiddle with counter..

$samples = $registry->collect();
```
