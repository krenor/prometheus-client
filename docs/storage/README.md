Storage
=======

## Introduction

Usually PHP processes don't share any state so metrics have to be stored elsewhere. Some of the  
provided storages might require additional extensions (e.g. APCU) and/or packages (e.g. Predis).  

Currently these storages are supported: 

* [APCU](APCU.md)
* [InMemory](IN_MEMORY.md)
* [Memcached](MEMCACHED.md)
* [Redis](REDIS.md)

## StorageManager
 
The `StorageManager` implements the `Storage` interface by taking a [`Repository`](#repositories) implementation  
and gets rid of all the things like error handling, prefixing and such. 

### How are metrics stored?

#### Legend

* `<name>`: Fully-qualified metric name (`namespace`_`name`)
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
  <labels as json> => <prefix>:<name>:<crc32(json(labels))>:VALUES
````

## Repositories

Repositories are the implementation used by the [StorageManager](#storagemanager) to store (metrics) data.  
For storages that don't offer complex data types such as Redis or return `false` rather than  
throwing exceptions (APCU, Memcached, ...)  there's the `SimpleRepository`.  By extending  
it you only have to take care of the actual calls to retrieve, store and flush data. 
