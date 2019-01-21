Bindings
========

## Introduction

Bindings are used by the [StorageManager](README.md#storagemanager) to allow for customization of how metrics are handled.  
They also aim to make the use of [custom metrics](../metrics/CUSTOM.md) easier.

## Structure

All bindings are having the [repository](repositories/README.md) and the key used for storage injected via their constructor.  
Bindings only need to implement a single method: `__invoke`.  
Its parameters and return type will differ by the binding type.

## Types

### Collectors

Defines how metrics are collected and in which [`SamplesBuilder`][samples-builder] they're passed into.  
Metrics might not only store data using the default key but also use additional keys  
(e.g. [histograms using an additional key for summarized values](README.md#histograms)). 

The `__invoke` method **must** return a [`SamplesBuilder`][samples-builder].

#### Example

```php
<?php

namespace Acme\Storage\Bindings\Collectors;

use Acme\Metrics\Custom;
use Krenor\Prometheus\Contracts\Binding;
use Tightenco\Collect\Support\Collection;
use Acme\Storage\Builders\CustomSamplesBuilder;

class CustomCollector extends Binding {
    /**
     * @param Custom $metric
     * @param Collection $items
     *
     * @return CustomSamplesBuilder
     */
    public function __invoke(Custom $metric, Collection $items): CustomSamplesBuilder
    {
        return new CustomSamplesBuilder($metric, $items->merge(...));
    }
}
```

Notice that `$items` are the items retrieved by the key used for storage.

### Observers

Defines how metrics implementing the `Observable` contract are stored.  
For example, both [histograms](README.md#histograms) and [summaries](README.md#summaries) implement `Observable`, 
however, they differ in how they store the data.

#### Example

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

Notice that `$labels` are a collection of combined label names **and** values.

[samples-builder]: SAMPLES_BUILDER.md
