Push Gateway
============

## Introduction

The Prometheus Pushgateway exists to allow ephemeral and batch jobs to expose their  
metrics to Prometheus. Since these kinds of jobs may not exist long enough to be scraped,  
they can instead push their metrics to a Pushgateway. The Pushgateway then exposes  
these metrics to Prometheus.

**However**: There are several pitfalls when blindly using the Pushgateway instead of Prometheus's  
usual pull model for general metrics collection, therefore it's recommended to read the  [official  
documentation][push-gateway] first before using the Pushgateway.

The Pushgateway API Documentation can be [found here][push-gateway-api].

## Example

```php
<?php

use GuzzleHttp\Client;
use Krenor\Prometheus\PushGateway;
use Krenor\Prometheus\CollectorRegistry;

$client = new Client([
    'base_uri' => env('PUSH_GATEWAY_URL'),
]);

$registry = new CollectorRegistry;

// Fiddle with metrics..

$gateway = new PushGateway($client, $registry);

$gateway->add('some-job', 'some-instance');
$gateway->replace('another-job', 'another-instance');
$gateway->delete('yet-another-job', 'yet-another-instance');
```

[push-gateway]: https://prometheus.io/docs/practices/pushing/
[push-gateway-api]: https://github.com/prometheus/pushgateway#api
