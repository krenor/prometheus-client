## A Prometheus client library for PHP

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
As long as it's not tagged I could commit incompatible changes. Heck, I don't even have a CI setup or any tests yet. I'm currently experimenting using the redis storage.  
As soon as I feel its good enough for a 0.1 release I'll start working towards the [guidelines](https://prometheus.io/docs/instrumenting/writing_clientlibs/) 
of writing a prometheus client and implement at least all **MUST** and **SHOULD** requirements. If some of them can't be met I'll add them to a list explaining why.
