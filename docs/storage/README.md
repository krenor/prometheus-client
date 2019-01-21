Storage
=======

## Introduction

Usually PHP processes don't share any state so metrics have to be stored elsewhere.  
Storages are an abstraction of [repositories][repository-docs] which interact with metrics.

## StorageManager
 
Implements the `Storage` interface by taking a [`Repository`][repository-docs] implementation and helps with  
of all the annoying little things, e.g. error handling, extensibility through [bindings][bindings-docs], prefixing etc.

### Methods

#### `collect(Metric $metric)`

Retrieve samples of a metric

#### `increment(Incrementable $metric, float $value, array $labels = [])`

Increment a metric with given label values by `$value`

#### `decrement(Decrementable $metric, float $value, array $labels = [])`

Decrement a metric with given label values by `$value`

#### `observe(Observable $metric, float $value, array $labels = [])`

Observe a metric with given label values with `$value`

#### `set(Settable $metric, float $value, array $labels = [])`

Set a metric with given label values to `$value`

#### `flush()`

Flush the underlying [repository][repository-docs]

#### `bind(string $key, string $metric, string $collector)`

Associate the [`$binding`][bindings-docs] namespace for `$metric` instances

Available keys are:
* [`StorageManager::COLLECTOR_BINDING_KEY`](BINDINGS.md#collectors)
* [`StorageManager::OBSERVER_BINDING_KEY`](BINDINGS.md#observers)

### Inner workings - how are metrics stored?

#### Legend

* `<name>`: Fully-qualified metric name (`{namespace}_{name}`)
* `<labels>`: Label names and values combined.
* `<prev>`: Previously stored value or 0 (summaries use an empty array instead)

#### [Counters](../metrics/COUNTERS.md)

```
<prefix>:<name> =>
  <json(labels)> => <prev> += <value>
```

#### [Gauges](../metrics/GAUGES.md)

Identical to Counters

#### [Histograms](../metrics/HISTOGRAMS.md)

##### Observations
```
<prefix>:<name> => 
  <json(labels + bucket)> => <prev> += 1
```

##### Sums
```
<prefix>:<name>:SUM =>
  <json(labels)> => <prev> += <value>
```

#### [Summaries](../metrics/SUMMARIES.md)

##### Observations
```
<prefix>:<name>:<crc32(json(labels))>:VALUES => 
  <prev>[] = <value>
```

##### Track of Observations
```
<prefix>:<name> =>
  <json(labels)> => <prefix>:<name>:<crc32(json(labels))>:VALUES
```

[repository-docs]: repositories/README.md
[bindings-docs]: BINDINGS.md
