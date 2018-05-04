## A Prometheus client library for PHP

Monitor your PHP applications using [Prometheus](https://prometheus.io).  

[![Packagist][icon-version]][link-version]
[![Travis][icon-travis]][link-travis]
[![Quality][icon-code-quality]][link-code-quality]
[![Coverage][icon-code-coverage]][link-code-coverage]
[![License][icon-license]][link-license]


## Features

- Support for all four metric types (Counters, Gauges, Histograms, Summaries)
- Various storage adapters (APCU, In-memory, Memcached, Redis)
- Rendering to text format.
- Easy usage (in Laravels [Eloquent](https://laravel.com/docs/5.6/eloquent) style)
- Initialization of Counters (**WIP**)

## Missing features

- Pushing metrics to a Pushgateway
- Rendering to Protocol buffer format

  
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
$predis = new \Predis\Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
]);

$repository = new RedisRepository($predis);

Metric::storeUsing(new StorageManager($repository);

$registry = new CollectorRegistry;

$counter = $registry->register(new ExampleCounter);

$counter->inc(['some', 'example', 'labels']);
$counter->incBy(3, ['diffent', 'label', 'values']);
```

~~For more detailed examples, please see the [API Documentation](docs/README.md)~~  
**In the works, but for now [read the tests](tests/Integration/TestCase.php).**

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

