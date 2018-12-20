APCU
====

## Introduction

To use this repository the `apcu` extension has to be installed.  
If used inside the CLI  make sure to  enable it via [`apc.enable_cli`][apc-cli].

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\ApcuRepository;

Metric::storeUsing(new StorageManager(new ApcuRepository));
```

[apc-cli]: http://php.net/manual/en/apc.configuration.php#ini.apc.enable-cli
