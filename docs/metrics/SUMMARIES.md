Summaries
=========

## Introduction

Similar to a histogram, a summary samples observations. While it also provides a total count  
of observations and a sum of all observed values, it calculates configurable quantiles over  
a sliding time window.

A summary with a base metric name of `<basename>` exposes multiple time series during a scrape:

* streaming φ-quantiles (0 ≤ φ ≤ 1) of observed events, exposed as `<basename>{quantile="<φ>"}`
* the total sum of all observed values, exposed as `<basename>_sum`
* the count of events that have been observed, exposed as `<basename>_count`

See [the documentation][histograms-vs-summaries] for the comprehensive differences of histograms and summaries.

## Properties

As summaries extend the [abstract Metric](README.md) they offer the same [properties](README.md#properties).  
Additionally they have the following properties:

#### `array $quantiles`

An array of quantiles to calculate on observations.  This property can be omitted to  
use the default quantiles (`.01`, `.05`, `.5`, `.9`, `.99`).

Sorting the quantiles isn't required as its done silently.  

## Methods

As summaries extend the [abstract Metric](README.md) they offer the same [methods](README.md#methods).    
Besides a `quantiles()` getter they offer the following additional methods:

#### `observe(float $value, array $labels = [])`

Pass a call to the underlying [Storage](./storage/README.md) to observe this summary with `$value` and 
given **label values**.

#### `chronometer(array $labels = [], int $precision = 4)`

[Returns a closure to track execution time](TRACKING_EXECUTION_TIME.md)

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Summary;

$summary = new class extends Summary {
    protected ?string $namespace = 'example';
    
    protected string $name = 'summary';

    protected string $description = 'Example Summary.';

    protected array $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    protected array $quantiles = [
        .33,
        .66,
        .99,
    ];
}

$counter->observe(42, ['foo', 'bar', 'baz']);
```

[histograms-vs-summaries]: https://prometheus.io/docs/practices/histograms/
