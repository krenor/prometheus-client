## A Prometheus client library for PHP

[![Travis][icon-travis]][link-travis]
[![Quality][icon-code-quality]][link-code-quality]
[![Coverage][icon-code-coverage]][link-code-coverage]

This library helps monitoring a PHP application using [Prometheus](https://prometheus.io).  
It aims to be a modern alternative to [Jimdo's Prometheus client](https://github.com/Jimdo/prometheus_client_php) with features like:
* PHP 7.1+  
* Easier usage (in Laravels [Eloquent](https://laravel.com/docs/5.6/eloquent) style)
* Initialization of Counters
* Summary Metrics support
* Predis storage support  
* Memcached storage support  
* and more!™
  
## Project State

**This library is currently under development.**  
As long as it's not tagged I could commit incompatible changes.  
As soon as I feel its good enough for a 0.1 release I'll start working towards the [guidelines](https://prometheus.io/docs/instrumenting/writing_clientlibs/) 
of writing a prometheus client and implement at least all **MUST** and **SHOULD** requirements. If some of them can't be met I'll add them to a list explaining why.

[icon-travis]: https://img.shields.io/travis/krenor/prometheus-client.svg?style=flat-square
[icon-code-quality]: https://img.shields.io/scrutinizer/g/krenor/prometheus-client.svg?style=flat-square
[icon-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/krenor/prometheus-client.svg?style=flat-square

[link-travis]: http://travis-ci.org/krenor/prometheus-client
[link-code-quality]: https://scrutinizer-ci.com/g/krenor/prometheus-client
[link-code-coverage]: https://scrutinizer-ci.com/g/krenor/prometheus-client
