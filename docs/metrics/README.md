Metrics
=======

## Introduction

The way metrics are handled differs from known libraries like 
[Jimdo/prometheus_client_php](https://github.com/Jimdo/prometheus_client_php).  
This library is heavily inspired by the approach of the 
[Laravel Eloquent ORM](https://laravel.com/docs/master/eloquent).  
All metric data besides their values is read-only to prevent altering during runtime.  
That is, unless you use reflections or the like. 

## Children

* [`Counter`](COUNTERS.md)
* [`Gauge`](GAUGES.md)
* [`Histogram`](HISTOGRAMS.md)
* [`Summary`](SUMMARIES.md)

## Properties

#### `namespace`

A metric name should have a (single-word) application prefix (**aka namespace**) relevant to the domain  
the metric belongs to. For metrics specific to an application, the prefix is usually the application name  
itself. Sometimes, however, the namespace can be more generic like `http` or `process`.

#### `name`

The metric name specifies the general feature of a system that is measured (e.g. `requests_total`).  
It may contain ASCII letters and digits, as well as underscores and colons.  
It **must** match the regex `^[a-zA-Z_:][a-zA-Z0-9_:]*$`.

#### `description`

A description of what this metric is all about. 

#### `labels`

When you have multiple metrics that you want to add/average/sum, they should usually be one  
metric with labels rather than multiple metrics. For example, rather than  `http_responses_500_total`  
and `http_responses_403_total`, create a single metric called `http_responses_total` with a label  
named `code` for the HTTP response code.  

Label names may contain ASCII letters, numbers, as well as underscores. They must match  
the regex `^[a-zA-Z_][a-zA-Z0-9_]*$`.  Label names beginning with `__` are reserved for internal use.

While labels are very powerful, **avoid overly granular metric labels**. The combinatorial explosion  
of breaking out a metric in many dimensions can produce huge numbers of timeseries, which  
will then take longer and more resources to process. 

#### `storage`

As you know PHP doesn't have persistent processes therefore all metric values have to go into a storage.  
This static property is only required to be set once (that is, unless you want to store in two separate storages). 

## Methods

#### `key()`

Returns the fully qualified metric name consisting of the namespace and name property.  
The [naming regex rule](#name) apply to this, too.

#### `type()`

Returns the type of this metric.

#### `storeUsing(Storage $storage)`

Set the storage for this and all extending metrics.

**All other methods are getters named exactly like the property itself.**
