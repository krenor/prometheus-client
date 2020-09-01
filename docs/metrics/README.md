Metrics
=======

## Introduction

The way metrics are handled differs from known libraries like [Jimdo/prometheus_client_php][jimdo-prometheus].  
This library is heavily inspired by the approach of the [Laravel Eloquent ORM][laravel-eloquent].  
All metric data besides their values is read-only to prevent altering during runtime.  
That is, unless you use reflections or the like. 

Note that all further documentation refers to `Krenor\Prometheus\Metrics\Metric`, not  
its implemented `Krenor\Prometheus\Contracts\Metric` interface.

## Classes

* [`Counter`](COUNTERS.md)
* [`Gauge`](GAUGES.md)
* [`Histogram`](HISTOGRAMS.md)
* [`Summary`](SUMMARIES.md)

## Traits

* [`TracksExecutionTime`](TRACKING_EXECUTION_TIME.md) 

## Properties

#### `?string $namespace`

A metric name should have a (single-word) application prefix (**aka namespace**) relevant to the domain  
the metric belongs to. For metrics specific to an application, the prefix is usually the application name  
itself. Sometimes, however, the namespace can be more generic like `http` or `process`.

#### `string $name`

The metric name specifies the general feature of a system that is measured (e.g. `requests_total`).  
It may contain ASCII letters and digits, as well as underscores and colons.  
It **must** match the regex `^[a-zA-Z_:][a-zA-Z0-9_:]*$`.

#### `string $description`

A description of what this metric is all about 

#### `array $labels`

When you have multiple metrics that you want to add/average/sum, they should usually be one  
metric with labels rather than multiple metrics. For example, rather than  `http_responses_500_total`  
and `http_responses_403_total`, create a single metric called `http_responses_total` with a label  
named `code` for the HTTP response code.  

Label names **may** contain ASCII letters, numbers, as well as underscores.  
They **must** match the regex `^[a-zA-Z_][a-zA-Z0-9_]*$`.  
Label names **must not** beginn with `__` as these are reserved for internal use.

While labels are very powerful, **avoid overly granular metric labels**. The combinatorial explosion  
of breaking out a metric in many dimensions can produce huge numbers of timeseries, which  
will then take longer and more resources to process. 

#### `Storage $storage`

PHP doesn't have persistent processes therefore all metric values have to be stored elsewhere

## Methods

#### `key()`

Returns the fully qualified metric name consisting of the namespace and name property.  
The [naming regex rule](#string-name) apply to this, too.

#### `type()`

Returns the type of this metric

#### `static storeUsing(Storage $storage)`

Set the storage for this and all extending metrics

**All other methods are getters named exactly like the property itself.**

[jimdo-prometheus]: https://github.com/Jimdo/prometheus_client_php
[laravel-eloquent]: https://laravel.com/docs/master/eloquent
