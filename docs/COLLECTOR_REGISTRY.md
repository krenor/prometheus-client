CollectorRegistry
=================

## Introduction

The `CollectorRegistry` is.. well, a collector registry. It registers metrics and collects  
`MetricFamilySamples` from them.  By default only the [supported metric](metrics/README.md#Children) are handled.  
If you want to use custom metrics you'll have to extend the functionality.

## Methods

#### `collect()`

Collects `Samples` from all registered metrics and maps them to a collection of `MetricFamilySamples`.

#### `register(Metric $metric)`

Registers the metric by its namespace if its not already registered, returning the given `$metric`.

#### `unregister(Metric $metric)`

Lets the registry forget this metric from its registry.

**For each metric there is an equally named plural/singular getter** as to access either
* the collection of a metric type 
* or a metric from said collection by its namespace

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

// increment counter..

$samples = $registry->collect();
```
