## A Prometheus client library for PHP

Monitor your PHP applications using [Prometheus](https://prometheus.io).  

[![Packagist][icon-version]][link-version]
[![icon-php]][link-version]
[![Travis][icon-travis]][link-travis]
[![Quality][icon-code-quality]][link-code-quality]
[![Coverage][icon-code-coverage]][link-code-coverage]
[![License][icon-license]][link-license]

## Features

- Support for Counters, Gauges, Histograms, Summaries and custom metrics
- Various [storage repositories](docs/storage/repositories/README.md)
- Easy usage in style of [Laravels Eloquent ORM](https://laravel.com/docs/master/eloquent)
- Initialization of Metrics without labels
- Support of float values
- Push Gateway
- State exporters (for `fpm_get_status()` or `opcache_get_status()`)

## Planned features

- PHP 7.4 rewrite
- Laravel integration
  
## Project State

### This library is currently under development.

As long as it's not tagged in a >= 1.* version I **could** commit incompatible changes!  

## Quickstart

```php
<?php

use Krenor\Prometheus\Metrics\Metric;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\CollectorRegistry;
use Krenor\Prometheus\Renderer\TextRenderer;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsCounterStub as ExampleCounter;

Metric::storeUsing(new StorageManager(new InMemoryRepository));

$registry = new CollectorRegistry;

/** @var Counter $counter */
$counter = $registry->register(new ExampleCounter);

$counter->increment(['some', 'label', 'values']);
$counter->incrementBy(3, ['foo', 'bar', 'baz']);

$samples = $registry->collect();
$metrics = (new TextRenderer)->render($samples);
```

A more detailed documentation can be [found here](docs/README.md).
    
**Note: Since this project is in the works, some parts may lack documentation.  
You can [orient yourself on the tests](tests/Integration/TestCase.php) if something's unclear.**

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more information.

## Licence

The MIT License. Please see [LICENSE](LICENSE.md) for more information.

[icon-php]: https://img.shields.io/packagist/php-v/krenor/prometheus-client?color=%234F5B93&label=PHP&style=flat-square
[icon-version]: https://img.shields.io/packagist/v/krenor/prometheus-client.svg?style=flat-square
[icon-travis]: https://img.shields.io/travis/krenor/prometheus-client.svg?style=flat-square
[icon-code-quality]: https://img.shields.io/scrutinizer/g/krenor/prometheus-client.svg?style=flat-square
[icon-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/krenor/prometheus-client.svg?style=flat-square
[icon-license]: https://img.shields.io/github/license/krenor/prometheus-client.svg?style=flat-square

[link-version]: https://packagist.org/packages/krenor/prometheus-client
[link-travis]: http://travis-ci.org/krenor/prometheus-client
[link-code-quality]: https://scrutinizer-ci.com/g/krenor/prometheus-client
[link-code-coverage]: https://scrutinizer-ci.com/g/krenor/prometheus-client
[link-license]: https://github.com/krenor/prometheus-client/blob/master/LICENSE.md
