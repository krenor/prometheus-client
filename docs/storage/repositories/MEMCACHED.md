Memcached
=========

## Introduction

To use this repository the `memcached` extension has to be installed.

## Example

```php
<?php

use Memcached;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\MemcachedRepository;

$memcached = new Memcached;

$memcached->addServer(...);

Metric::storeUsing(new StorageManager(
    new MemcachedRepository($memcached)
));
```
