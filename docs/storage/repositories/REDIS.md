Redis
=====

## Introduction

To use this repository either the [phpredis][phpredis] extension or the [predis client][predis] is required.

## Example

### [PhpRedis][phpredis]

```php
<?php

use Redis;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Redis\PhpRedisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

$redis = new Redis;

$redis->connect(...);

$connection = new PhpRedisConnection($redis);

Metric::storeUsing(new StorageManager(
    new RedisRepository($connection)
));
```

### [Predis][predis]

```php
<?php

use Predis\Client as Redis;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Redis\PredisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

$redis = new Redis(...);

$connection = new PredisConnection($client);

Metric::storeUsing(new StorageManager(
    new RedisRepository($connection)
));
```

[predis]: https://github.com/nrk/predis
[phpredis]: https://github.com/phpredis/phpredis
