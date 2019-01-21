Repositories
============

## Introduction

Repositories are the implementation used by the [StorageManager](../README.md#storagemanager) to store the metrics data.  
For storages that don't offer as complex data types as Redis or return `false` rather than  
throwing exceptions (looking at you, [APCU][apcu-store], [Memcached][memcached-set], ...)  there's the  `SimpleRepository`.  
By extending it you only have to take care of the actual calls to retrieve, store and flush data. 

## Supported Repositories

* [APCU](APCU.md)
* [InMemory](IN_MEMORY.md)
* [Memcached](MEMCACHED.md)
* [Redis](REDIS.md) (both Predis and native)

[apcu-store]: http://php.net/manual/en/function.apcu-store.php#refsect1-function.apcu-store-returnvalues
[memcached-set]: http://php.net/manual/en/memcached.set.php#refsect1-memcached.set-returnvalues
