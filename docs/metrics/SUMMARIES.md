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

See [histograms and summaries][histograms-summaries] for differences to histograms.

## Properties

As summaries extend the [Metric class](README.md) they offer the same properties.  
Additionally they have the following properties:

#### `quantiles`

An array of quantiles to calculate on observations.  This property can be omitted to  
use the default quantiles (`.01`, `.05`, `.5`, `.9`, `.99`).

Sorting the quantiles isn't required as its done silently.  

## Methods

As summaries extend the [Metric class](README.md) they offer the same methods.    
Besides a `quantiles()` getter they offer the following additional functionality:

#### `observe(float $value, array $labels)`

Pass a call to the [Storage][storage-docs] to observe this summary by `$value` with the given **label values**.

#### `chronometer(array $labels, int $precision)`

Returns a closure to track execution time. See the [TracksExecutionTime Trait][tracking-code-docs] for more information.

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Summary;

$summary = new class extends Summary {
    protected $namespace = 'example';
    
    protected $name = 'summary';

    protected $description = 'Example Summary.';

    protected $labels = [
        'example_label',
        'other_label',
        'yet_another_label',
    ];

    protected $quantiles = [
        .33,
        .66,
        .99,
    ];
}
```

[histograms-summaries]: https://prometheus.io/docs/practices/histograms/
[storage-docs]: ../storage/README.md
[tracking-code-docs]: TRACKING_EXECUTION_TIME.md
