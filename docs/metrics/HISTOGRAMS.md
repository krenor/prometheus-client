Histograms
==========

## Introduction

A histogram samples observations (usually things like request durations or response sizes)  
and counts them in configurable buckets. It also provides a sum of all observed values.

A histogram with a base metric name of `<basename>` exposes multiple time series during a scrape:

* cumulative counters for the observation buckets, exposed as  
`<basename>_bucket{le="<upper inclusive bound>"}`
* the total sum of all observed values, exposed as `<basename>_sum`
* the count of events that have been observed, exposed as `<basename>_count`  
(identical to `<basename>_bucket{le="+Inf"}` above)

## Properties

As histograms extend the [abstract Metric](README.md) they offer the same [properties](README.md#properties).  
Additionally they have the following properties:

#### `array $buckets`

An array of inclusive upper bounds to count observations.  This property can be omitted to  
use the default buckets (`.005`, `.01`, `.025`, `.05`, `.1`, `.25`, `.5`, `1`, `2.5`, `5`, `10`) which  
are intended to cover a typical web/rpc request in seconds.

Sorting the buckets isn't required as its done silently.  
You **shouldn't** include an `+Inf` bucket as 
* it's added dynamically during collection
* the buckets are internally [treated as floats][string-to-float-conversion]

**Each bucket is one timeseries.** Many buckets and/or many dimensions with labels can  
produce large amount of time series, that may cause performance problems.

## Methods

As histograms extend the [abstract Metric](README.md) they offer the same [methods](README.md#methods).    
Besides a `buckets()` getter they offer the following additional methods:

#### `observe(float $value, array $labels = [])`

Pass a call to the underlying [Storage](../storage/README.md) to observe this histogram with `$value` and 
given **label values**

#### `chronometer(array $labels = [], int $precision = 4)`

[Returns a closure to track execution time](TRACKING_EXECUTION_TIME.md)

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Histogram;

$histogram = new class extends Histogram {
    protected string $namespace = 'example';
    
    protected string $name = 'histogram';

    protected string $description = 'Example Histogram.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    protected array $buckets = [
        .5,
        1.25,
        2.25,
    ];
}

$histogram->observe(42, ['foo', 'bar', 'baz']);
```

[string-to-float-conversion]: http://php.net/manual/en/language.types.string.php#language.types.string.conversion
