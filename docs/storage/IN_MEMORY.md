In-Memory
=========

## Example

```php
<?php

use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

Metric::storeUsing(new StorageManager(new InMemoryRepository));
```
