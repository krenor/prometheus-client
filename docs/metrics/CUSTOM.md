Custom Metrics
==============

## Introduction

If none of the supported metrics fulfills your requirements you can register your own metric type.  
For the sake of simplicity the custom metric can only `observe()` values and is initialized with `0`.

## Steps

### 0. Create your custom metric type

```php
<?php

namespace Acme\Metrics;

use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Contracts\Types\Observable;

abstract class Custom extends Metric implements Observable
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return 'custom';
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function observe(float $value, array $labels = []): Observable
    {
        static::$storage->observe($this, $value, $labels);

        return $this;
    }
}
```

### 1. Create a [`CollectorRegistry`](../COLLECTOR_REGISTRY.md) capable of handling the custom metric type

```php
<?php

namespace Acme;

use Exception;
use Acme\Prometheus\Metrics\Custom;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\CollectorRegistry;
use Tightenco\Collect\Support\Collection;

class CustomCollectorRegistry extends CollectorRegistry
{
    /**
     * @var Collection
     */
    protected $custom;
    
    /**
     * CustomCollectorRegistry constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->custom = new Collection;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function collector(Metric $metric): Collection
    {
        try {
            parent::collector($metric);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'unknown metric type') !== false && $metric instanceof Custom) {
                return $this->custom;
            }
            
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function collectible(): Collection
    {
        return parent::collectible()
                     ->push($this->custom);
    }
}
```

### 2. Create a [`SamplesBuilder`](../storage/SAMPLES_BUILDER.md) capable of forming samples from the data

```php
<?php

namespace Acme\Storage\Builders;

use Closure;
use Acme\Metrics\Custom;
use Krenor\Prometheus\Sample;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\SamplesBuilder;

class CustomSamplesBuilder extends SamplesBuilder
{
    /**
     * @var Custom
     */
    protected $metric;

    /**
     * CustomSamplesBuilder constructor.
     *
     * @param Custom $metric
     * @param Collection $items
     */
    public function __construct(Custom $metric, Collection $items)
    {
        parent::__construct($metric, $items);
    }

    /**
     * @return int
     */
    protected function initialize(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function group(): Collection
    {
        // Create samples based on how data has been stored..
    }
}
```

### 3. Create [Bindings](../storage/BINDINGS.md)

#### 3.1 Collector

```php
<?php

namespace Acme\Storage\Bindings\Collectors;

use Acme\Metrics\Custom;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;
use Acme\Storage\Builders\CustomSamplesBuilder;

class CustomCollector extends Binding 
{
    /**
     * @param Custom $metric
     * @param Collection $items
     *
     * @return CustomSamplesBuilder
     */
    public function __invoke(Custom $metric, Collection $items): CustomSamplesBuilder
    {
        return new CustomSamplesBuilder($metric, $items->...);
    }
}
```

#### 3.2 Observer

```php
<?php

namespace Acme\Storage\Bindings\Observers;

use Acme\Metrics\Custom;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;

class CustomObserver extends Binding
{
    /**
     * @param Custom $metric
     * @param Collection $labels
     * @param float $value
     *
     * @return void
     */
    public function __invoke(Custom $metric, Collection $labels, float $value): void
    {
        // Interact with the repository and store the data...
    }
}
```

### 4. Register Bindings to the [`StorageManger`](../storage/README.md#storagemanager)

```php
<?php

use Acme\Metrics\Custom;
use Krenor\Prometheus\Storage\StorageManager;
use Acme\Storage\Bindings\Observers\CustomObserver;
use Acme\Storage\Bindings\Observers\CustomCollector;
use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

$storage = new StorageManager(new InMemoryRepository);

$storage->bind(StorageManager::COLLECTOR_BINDING_KEY, Custom::class, CustomCollector::class)
        ->bind(StorageManager::OBSERVER_BINDING_KEY, Custom::class, CustomObserver::class);
```

### 5. ???

### 6. Profit
