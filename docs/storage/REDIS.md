Redis
=====

## Introduction

To use this repository the [predis client][predis] is required.

## Example

```php
<?php

use Predis\Client;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

$client = new Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
]);

Metric::storeUsing(new StorageManager(
    new RedisRepository($client)
));
```

[predis]: https://github.com/nrk/predis
