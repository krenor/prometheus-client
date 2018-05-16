## A Prometheus client library for PHP

Monitor your PHP applications using [Prometheus](https://prometheus.io).  

[![Packagist][icon-version]][link-version]
[![Travis][icon-travis]][link-travis]
[![Quality][icon-code-quality]][link-code-quality]
[![Coverage][icon-code-coverage]][link-code-coverage]
[![License][icon-license]][link-license]


## Features

- Support for Counters, Gauges, Histograms, Summaries
- Various storage adapters (APCU, In-Memory, Memcached, Redis)
- Easy usage in style of [Laravels Eloquent ORM](https://laravel.com/docs/5.6/eloquent)
- Initialization of Counters (**W.I.P.**)

## Missing features

- Pushing metrics to a Pushgateway
  
## Project State

**This library is currently under development.**  
As long as it's not tagged in a >= 1.* version I **could** commit incompatible changes.  
Especially since I'll start working towards the [guidelines](https://prometheus.io/docs/instrumenting/writing_clientlibs/) 
of writing a prometheus client and implement at least all **MUST** and **SHOULD** requirements. If some of them can't be met I'll add them to a list explaining why.

## Unmet guidelines

#### Labels

>A client library MUST allow for optionally specifying a list of label names at Gauge/Counter/Summary/Histogram creation time.
  
This library takes the approach of labels already being defined on the metrics rather than via a constructor/setter/options class.

## Example

```php
Metric::storeUsing(new StorageManager(new InMemoryRepository);

$registry = new CollectorRegistry;

/** @var Counter $counter */
$counter = $registry->register(new ExampleCounter);

$counter->increment(['some', 'example', 'labels']);
$counter->incrementBy(3, ['diffent', 'label', 'values']);

$samples = $registry->collect();
$metrics = (new TextRenderer)->render($samples);
```


For more detailed examples, please see the [API Documentation](docs/README.md).
    
**Note: As this project is in the works, some parts might not be documented yet.  
You can always [read the tests](tests/Integration/TestCase.php) if something is unclear.**

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more information.

## Licence

The MIT License. Please see [LICENSE](LICENSE.md) for more information.

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
