Exporters
=========

## Introduction

Exporters are used to export volatile external states, such as the [FPM status][fpm-status] or  
[Opcache status][opcache-status], both of which are supported by this package. 

## Example

### FPM

```php
<?php

use Krenor\Prometheus\Exporter\FPM;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Renderer\TextRenderer;

$fpm = new FPM(fpm_get_status());

$metrics = (new Collection([
    $fpm->uptime(),
    $fpm->connections(),
    $fpm->queued(),
    $fpm->maxQueued(),
    $fpm->queue(),
    $fpm->idle(),
    $fpm->active(),
    $fpm->total(),
    $fpm->maxActive(),
    $fpm->maxSpawned(),
    $fpm->slow(),
]))->concat($fpm->processes());

$states = (new TextRenderer)->render($metrics);
```

### OPcache

```php
<?php

use Krenor\Prometheus\Exporter\Opcache;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Renderer\TextRenderer;

$opcache = new Opcache(opcache_get_status(false));

$metrics = (new Collection([
    $opcache->enabled(),
    $opcache->oom(),
    $opcache->restarts(),
    $opcache->restarting(),
    $opcache->consumed(),
    $opcache->free(),
    $opcache->wasted(),
    $opcache->wastePercentage(),
    $opcache->stringsBuffer(),
    $opcache->stringsMemoryConsumed(),
    $opcache->stringsMemoryFree(),
    $opcache->strings(),
    $opcache->scripts(),
    $opcache->keys(),
    $opcache->maxCachedKeys(),
    $opcache->hits(),
    $opcache->started(),
    $opcache->restart(),
    $opcache->oomRestarts(),
    $opcache->hashRestarts(),
    $opcache->manualRestarts(),
    $opcache->misses(),
    $opcache->blacklistMisses(),
    $opcache->blacklistMissRatio(),
    $opcache->hitRatio(),
]));

$states = (new TextRenderer)->render($metrics);
```

[fpm-status]: https://easyengine.io/tutorials/php/fpm-status-page/
[opcache-status]: https://www.php.net/manual/en/function.opcache-get-status.php
