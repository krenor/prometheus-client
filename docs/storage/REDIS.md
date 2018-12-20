Redis
=====

## Introduction

To use this repository either the [phpredis][phpredis] extension or the [predis client][predis] is required.

## Example

### PhpRedis

```php
<?php

use Redis;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Redis\PhpRedisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

$redis = new Redis;

$redis->connect(
    getenv('REDIS_HOST'),
    getenv('REDIS_PORT')
);

$connection = new PhpRedisConnection($redis);

Metric::storeUsing(new StorageManager(
    new RedisRepository($connection)
));
```

### Predis

```php
<?php

use Predis\Client as Redis;
use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Redis\PredisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

$redis = new Redis([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
]);

$connection = new PredisConnection($client);

Metric::storeUsing(new StorageManager(
    new RedisRepository($connection)
));
```

[predis]: https://github.com/nrk/predis
[phpredis]: https://github.com/phpredis/phpredis
